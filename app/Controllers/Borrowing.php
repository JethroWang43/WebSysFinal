<?php
namespace App\Controllers;

class Borrowing extends BaseController
{
    protected function ensureBorrowData()
    {
        $session = session();
        if (!$session->has('borrows')) {
            $session->set('borrows', []);
            $session->set('borrows_next_id', 1);
            // history for returned items
            $session->set('borrow_history', []);
        }
    }

    public function index()
    {
        $this->ensureBorrowData();
        $session = session();
        $borrows = $session->get('borrows') ?? [];
        $history = $session->get('borrow_history') ?? [];

        // Current equipment items: prefer DB `tbequipment` as source of truth.
        // If DB is unavailable, fall back to any session-stored demo items.
        $equipment = [];
        try {
            $em = new \App\Models\EquipmentModel();
            $dbAll = $em->findAll();
            foreach ($dbAll as $r) {
                $n = $em->normalize($r);
                $equipment[$n['id']] = $n;
            }
        } catch (\Throwable $e) {
            // DB not available for some reason; use session-stored items instead
            $equipment = $session->get('equipment_items') ?? [];
        }

        // compute stats
        $now = new \DateTime();
        $active = count($borrows);
        $overdue = 0;
        $pending = 0; // due within 7 days
        foreach ($borrows as $b) {
            if (empty($b['due_date']))
                continue;
            try {
                $due = new \DateTime($b['due_date']);
                $diff = (int) $now->diff($due)->format('%r%a');
                if ($diff < 0) {
                    $overdue++;
                } elseif ($diff <= 7) {
                    $pending++;
                }
            } catch (\Exception $e) {
                // ignore parsing errors
            }
        }

        $totalHistory = $active + count($history);

        // Apply optional filtering for the borrows list (search and status)
        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $statusFilter = trim((string) ($this->request->getGet('status') ?? ''));

        $filtered = [];
        foreach ($borrows as $b) {
            // build haystack for search: ref, borrower name, id number, equipment name/id, description
            $hay = strtolower($b['ref'] ?? '');
            $hay .= ' ' . strtolower($b['borrower_name'] ?? '');
            $hay .= ' ' . strtolower($b['id_number'] ?? '');
            // equipment label from equipment items if available
            $equipName = '';
            $equipIdLabel = '';
            $equipDesc = '';
            if (!empty($equipment) && isset($equipment[$b['equipment_id']])) {
                $ei = $equipment[$b['equipment_id']];
                $equipName = strtolower($ei['name'] ?? '');
                $equipIdLabel = strtolower($ei['equipment_id'] ?? '');
                $equipDesc = strtolower($ei['description'] ?? '');
                $hay .= ' ' . $equipName . ' ' . $equipIdLabel . ' ' . $equipDesc;
            }

            // search filter
            if ($q !== '') {
                if (stripos($hay, strtolower($q)) === false) {
                    continue;
                }
            }

            // status filter based on due date
            $matchStatus = true;
            if ($statusFilter !== '' && $statusFilter !== 'all') {
                $matchStatus = false;
                if (!empty($b['due_date'])) {
                    try {
                        $now = new \DateTime();
                        $due = new \DateTime($b['due_date']);
                        $diff = (int) $now->diff($due)->format('%r%a');
                        if ($statusFilter === 'overdue' && $diff < 0)
                            $matchStatus = true;
                        if ($statusFilter === 'due_soon' && $diff >= 0 && $diff <= 7)
                            $matchStatus = true;
                        if ($statusFilter === 'active' && $diff >= 0)
                            $matchStatus = true;
                    } catch (\Exception $e) {
                        // ignore parse errors
                    }
                }
            }
            if (!$matchStatus)
                continue;

            $filtered[] = $b;
        }

        // Pagination for borrows
        $perPage = 6;
        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1)
            $page = 1;
        $all = array_values($filtered);
        $totalFiltered = count($all);
        $pages = max(1, (int) ceil($totalFiltered / $perPage));
        if ($page > $pages)
            $page = $pages;
        $offset = ($page - 1) * $perPage;
        $paged = array_slice($all, $offset, $perPage);

        $data = [
            'title' => 'Borrowings',
            'borrows' => $paged,
            'equipment_items' => $equipment,
            'stats' => [
                'active' => $active,
                'overdue' => $overdue,
                'pending' => $pending,
                'totalHistory' => $totalHistory,
            ],
            'q' => $q,
            'status' => $statusFilter,
            'page' => $page,
            'perPage' => $perPage,
            'totalFiltered' => $totalFiltered,
        ];

        return view('include\\head_view', $data)
            . view('include\\nav_view')
            . view('borrows_list_view', $data)
            . view('include\\foot_view');
    }

    public function create($equipmentId = null)
    {
        $this->ensureBorrowData();
        $session = session();
        // Prefer DB list for form dropdown; fall back to session demo items.
        $equipment = [];
        try {
            $em = new \App\Models\EquipmentModel();
            $dbAll = $em->findAll();
            foreach ($dbAll as $r) {
                $n = $em->normalize($r);
                $equipment[$n['id']] = $n;
            }
        } catch (\Throwable $e) {
            $equipment = $session->get('equipment_items') ?? [];
        }
        // if equipment not in session, try DB
        $equipmentItem = null;
        if ($equipmentId !== null && isset($equipment[$equipmentId])) {
            $equipmentItem = $equipment[$equipmentId];
        } elseif ($equipmentId !== null) {
            try {
                $em = new \App\Models\EquipmentModel();
                $row = $em->find($equipmentId);
                if ($row)
                    $equipmentItem = $em->normalize($row);
            } catch (\Throwable $e) {
                $equipmentItem = null;
            }
        }

        $usermodel = model('Users_model');
        $users = [];
        try {
            $users = $usermodel->findAll();
        } catch (\Exception $e) {
            $users = [];
        }

        // build users_meta similar to reservations: prefer StudentID from DB, else session extras
        $session = session();
        $user_extras = $session->get('user_extras') ?? [];
        $users_meta = [];
        foreach ($users as $u) {
            $sid = $u['StudentID'] ?? null;
            if (empty($sid) && isset($user_extras[$u['id']]['id_number'])) {
                $sid = $user_extras[$u['id']]['id_number'];
            }
            $users_meta[$u['id']] = [
                'id' => $u['id'],
                'name' => $u['fullname'] ?: $u['username'],
                'studentId' => $sid,
            ];
        }

        $data = [
            'title' => 'Borrow Equipment',
            'equipmentId' => $equipmentId,
            'equipment' => $equipmentItem ?? ($equipment[$equipmentId] ?? null),
            'equipment_items' => $equipment,
            'users' => $users,
            'users_meta' => $users_meta,
        ];

        return view('include\\head_view', $data)
            . view('include\\nav_view')
            . view('borrow_form_view', $data)
            . view('include\\foot_view');
    }

    public function submit()
    {
        $this->ensureBorrowData();
        $session = session();

        // VALIDATION RULES
        $rules = [
            'equipment_id' => 'required|integer',
            'user_id' => 'required|integer',
            'borrower_name' => 'required',
            'id_number' => 'required|numeric',
            // NOTE: The 'isTodayOrFuture' rule must be defined in your Validation config
            'due_date' => 'required|valid_date|isTodayOrFuture',
            'use_location' => 'required|min_length[3]',
        ];

        // MESSAGES
        $messages = [
            'equipment_id' => [
                'required' => 'Please select equipment.',
            ],
            'user_id' => [
                'required' => 'Please select a borrower.',
            ],
            'borrower_name' => [
                'required' => 'Borrower name is required.',
                'min_length' => 'Borrower name must be at least 3 characters.'
            ],
            'id_number' => [
                'required' => 'ID Number is required.',
                'numeric' => 'ID Number must contain digits only.'
            ],
            'due_date' => [
                'required' => 'A due date is required.',
                'valid_date' => 'Invalid date format.',
                'isTodayOrFuture' => 'Due date must be today or a future date.'
            ],
            'use_location' => [
                'regex_match' => 'Location may only contain letters, numbers, spaces, or dashes.'
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            // This is the correct way to pass validation errors to the next request (the view)
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // ----------------------------
        // Validation Passed — Proceed
        // ----------------------------

        $equipmentId = (int) $this->request->getPost('equipment_id');
        $user_id = $this->request->getPost('user_id');
        $borrower_name = $this->request->getPost('borrower_name');
        $id_number = $this->request->getPost('id_number');
        $borrower_email = $this->request->getPost('borrower_email');
        $due_date = $this->request->getPost('due_date');
        $use_location = $this->request->getPost('use_location');

        $borrows = $session->get('borrows') ?? [];
        $next = $session->get('borrows_next_id');

        // build ref number BR-YYYY-###
        $year = date('Y');
        $ref = sprintf('BR-%s-%03d', $year, $next);

        $record = [
            'id' => $next,
            'ref' => $ref,
            'equipment_id' => $equipmentId,
            'user_id' => $user_id,
            'borrower_name' => $borrower_name,
            'id_number' => $id_number,
            'borrower_email' => $borrower_email,
            'date_borrowed' => date('Y-m-d'),
            'due_date' => $due_date,
            'use_location' => $use_location ?? null,
        ];

        $borrows[$next] = $record;
        $session->set('borrows', $borrows);
        $session->set('borrows_next_id', $next + 1);

        // Update DB equipment status → Borrowed
        try {
            if (!empty($equipmentId)) {
                $em = new \App\Models\EquipmentModel();
                $update = [
                    'status' => 'Borrowed',
                    'last_updated' => date('Y-m-d H:i:s'),
                ];
                if (!empty($use_location))
                    $update['location'] = $use_location;

                $em->update($equipmentId, $update);
            }
        } catch (\Throwable $e) {
            // ignore DB failures
        }

        $session->setFlashdata('success', 'Borrow record created successfully.');

        return redirect()->to('borrowing');
    }


    public function return($id)
    {
        $this->ensureBorrowData();
        $session = session();
        $borrows = $session->get('borrows') ?? [];
        $history = $session->get('borrow_history') ?? [];
        $id = (int) $id;
        if (isset($borrows[$id])) {
            $equipmentId = $borrows[$id]['equipment_id'];
            // move to history
            $history[] = $borrows[$id];
            unset($borrows[$id]);
            $session->set('borrows', $borrows);
            $session->set('borrow_history', $history);

            // Also try to update the equipment status back to 'Available' in DB
            // and set its location back to the central ITSO location.
            try {
                if (!empty($equipmentId)) {
                    $em = new \App\Models\EquipmentModel();
                    $em->update($equipmentId, [
                        'status' => 'Available',
                        'location' => 'ITSO',
                        'last_updated' => date('Y-m-d H:i:s')
                    ]);
                }
            } catch (\Throwable $e) {
                // ignore DB errors
            }
        }

        return redirect()->to('borrowing/returns');
    }

    /**
     * Dedicated returns page: lists active borrows and allows returning
     */
    public function returns()
    {
        $this->ensureBorrowData();
        $session = session();
        $borrows = $session->get('borrows') ?? [];

        // merge equipment items from session and DB for labels
        // Prefer DB `tbequipment` for labels on the returns page; fall back to session data.
        $equipment = [];
        try {
            $em = new \App\Models\EquipmentModel();
            $dbAll = $em->findAll();
            foreach ($dbAll as $r) {
                $n = $em->normalize($r);
                $equipment[$n['id']] = $n;
            }
        } catch (\Throwable $e) {
            $equipment = $session->get('equipment_items') ?? [];
        }

        $data = [
            'title' => 'Return Equipment',
            'borrows' => $borrows,
            'equipment_items' => $equipment,
        ];

        return view('include\\head_view', $data)
            . view('include\\nav_view')
            . view('returns_view', $data)
            . view('include\\foot_view');
    }

    // show process returns page (select multiple and return)
    public function process()
    {
        $this->ensureBorrowData();
        $session = session();
        $borrows = $session->get('borrows') ?? [];

        $data = ['title' => 'Process Returns', 'borrows' => $borrows];

        return view('include\\head_view', $data)
            . view('include\\nav_view')
            . view('borrow_process_view', $data)
            . view('include\\foot_view');
    }

    public function processSubmit()
    {
        $this->ensureBorrowData();
        $session = session();
        $selected = $this->request->getPost('selected') ?? [];
        if (!is_array($selected))
            $selected = [$selected];

        foreach ($selected as $sid) {
            $this->return($sid);
        }

        return redirect()->to('borrowing');
    }
}
