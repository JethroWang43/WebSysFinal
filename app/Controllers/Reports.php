<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Reports extends BaseController
{
    /**
     * Ensures session data for borrowing exists (copied from Borrowing controller)
     */
    protected function ensureBorrowData()
    {
        $session = session();
        if (!$session->has('borrows')) {
            $session->set('borrows', []);
            $session->set('borrows_next_id', 1);
            $session->set('borrow_history', []);
        }
    }

    /**
     * Fetches equipment data from DB or falls back to session demo data.
     * @return array
     */
    protected function getEquipmentData(): array
    {
        $session = session();
        $equipment = [];
        try {
            // Assume a standard model setup for equipment
            $em = new \App\Models\EquipmentModel();
            $dbAll = $em->findAll();
            foreach ($dbAll as $r) {
                // Assuming normalize method exists on EquipmentModel
                $n = $em->normalize($r);
                $equipment[$n['id']] = $n;
            }
        } catch (\Throwable $e) {
            // DB not available, use session-stored items (e.g., demo data)
            $equipment = $session->get('equipment_items') ?? [];
        }
        return $equipment;
    }

    /**
     * Main method to generate and display reports.
     */
    public function index()
    {
        $this->ensureBorrowData();
        $session = session();
        $equipment_items = $this->getEquipmentData();

        // Get report type and search query from GET request
        $reportType = $this->request->getGet('type') ?? 'active_equipment';
        $q = trim((string) ($this->request->getGet('q') ?? ''));

        $reportData = [];
        $reportTitle = 'Report Generation';

        switch ($reportType) {
            case 'active_equipment':
                $reportTitle = 'Active Equipment List';
                foreach ($equipment_items as $item) {
                    // Active equipment: Status is NOT 'Unusable' or 'Maintenance'
                    $s = strtolower($item['status'] ?? 'available');
                    if ($s !== 'unusable' && $s !== 'maintenance') {
                        $hay = strtolower(($item['name'] ?? '') . ' ' . ($item['equipment_id'] ?? '') . ' ' . ($item['description'] ?? ''));
                        if ($q === '' || stripos($hay, strtolower($q)) !== false) {
                            $reportData[] = $item;
                        }
                    }
                }
                break;

            case 'unusable_equipment':
                $reportTitle = 'Unusable Equipment Report';
                foreach ($equipment_items as $item) {
                    // Unusable equipment: Status IS 'Unusable' or 'Maintenance'
                    $s = strtolower($item['status'] ?? '');
                    if ($s === 'unusable' || $s === 'maintenance') {
                        $hay = strtolower(($item['name'] ?? '') . ' ' . ($item['equipment_id'] ?? '') . ' ' . ($item['description'] ?? ''));
                        if ($q === '' || stripos($hay, strtolower($q)) !== false) {
                            $reportData[] = $item;
                        }
                    }
                }
                break;

            case 'user_borrowing_history':
                $reportTitle = 'User Borrowing History Report';
                $history = $session->get('borrow_history') ?? [];
                // Include active borrows in the history view for completeness
                $activeBorrows = $session->get('borrows') ?? [];

                // First include history records and mark them as returned (for older records without explicit return metadata)
                foreach ($history as $record) {
                    // ensure returned metadata so the view shows 'Returned'
                    if (empty($record['date_returned']) && empty($record['returned'])) {
                        $record['returned'] = 1;
                    }
                    $hay = strtolower(($record['borrower_name'] ?? '') . ' ' . ($record['id_number'] ?? '') . ' ' . ($record['ref'] ?? ''));
                    $equipment = $equipment_items[$record['equipment_id']] ?? [];
                    $hay .= ' ' . strtolower($equipment['name'] ?? '');
                    if ($q === '' || stripos($hay, strtolower($q)) !== false) {
                        $reportData[] = $record;
                    }
                }

                // Then include currently active borrows
                foreach ($activeBorrows as $record) {
                    $hay = strtolower(($record['borrower_name'] ?? '') . ' ' . ($record['id_number'] ?? '') . ' ' . ($record['ref'] ?? ''));
                    $equipment = $equipment_items[$record['equipment_id']] ?? [];
                    $hay .= ' ' . strtolower($equipment['name'] ?? '');
                    if ($q === '' || stripos($hay, strtolower($q)) !== false) {
                        $reportData[] = $record;
                    }
                }
                break;
        }

        // --- Paging/Filtering Logic for the Report ---
        $perPage = 10; // Defaulting to 10 for reports
        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1)
            $page = 1;

        $totalFiltered = count($reportData);
        $pages = max(1, (int) ceil($totalFiltered / $perPage));
        if ($page > $pages)
            $page = $pages;

        $offset = ($page - 1) * $perPage;
        $pagedReportData = array_slice($reportData, $offset, $perPage);
        // --- End Paging Logic ---

        $data = [
            'title' => 'Equipment Reports',
            'reportTitle' => $reportTitle,
            'reportType' => $reportType,
            'reportData' => $pagedReportData,
            'equipment_items' => $equipment_items,
            'q' => $q,
            'page' => $page,
            'perPage' => $perPage,
            'totalFiltered' => $totalFiltered,
        ];

        return view('include\\head_view', $data)
            . view('include\\nav_view')
            . view('reports_view', $data)
            . view('include\\foot_view');
    }
}
