<?php
namespace App\Controllers;

class Index extends BaseController {
    public function index() {
        // Prepare data for home dashboard including equipment stats (if available)
        $session = session();
        $items = $session->get('equipment_items') ?? [];

        $total = count($items);
        $available = count(array_filter($items, fn($i) => strtolower($i['status']) === 'available'));
        $borrowed = count(array_filter($items, fn($i) => strtolower($i['status']) === 'borrowed'));
        $maintenance = count(array_filter($items, fn($i) => strtolower($i['status']) === 'maintenance'));

        // borrows/reservations for recent activity and pending returns
        $borrows = $session->get('borrows') ?? [];
        $borrow_history = $session->get('borrow_history') ?? [];
        $reservations = $session->get('reservations') ?? [];

        // pending returns = active borrows
        $pendingReturns = count($borrows);
        $overdue = 0;
        $now = new \DateTime();
        foreach ($borrows as $b) {
            if (empty($b['due_date'])) continue;
            try {
                $due = new \DateTime($b['due_date']);
                $diff = (int)$now->diff($due)->format('%r%a');
                if ($diff < 0) $overdue++;
            } catch (\Exception $e) {}
        }

        $overduePercent = $pendingReturns ? round(($overdue / $pendingReturns) * 100, 1) : 0;

        // Build recent activity list from borrow_history, borrows and reservations
        $recent = [];
        // take last 8 events
        $addEvent = function($title, $subtitle, $minutesAgo, $type='info') use (&$recent) {
            $recent[] = ['title'=>$title,'subtitle'=>$subtitle,'minutes'=>$minutesAgo,'type'=>$type];
        };

        // borrow_history newest first
        $combined = array_reverse($borrow_history);
        foreach ($combined as $h) {
            $addEvent(($h['equipment_id'] ?? 'Item').' returned by '.($h['borrower_name'] ?? 'Someone'), '', rand(1,180), 'return');
        }
        foreach (array_reverse($borrows) as $b) {
            $addEvent(($b['equipment_id'] ?? 'Item').' borrowed by '.($b['borrower_name'] ?? 'Someone'), '', rand(5,240), 'borrow');
        }
        foreach (array_reverse($reservations) as $r) {
            $addEvent(($r['equipment_id'] ?? 'Item').' reserved by '.($r['name'] ?? 'Someone'), '', rand(30,1440), 'reserve');
        }

        // keep at most 6 recent events
        $recent = array_slice($recent, 0, 6);

        $chartData = [
            'labels' => ['Available','Borrowed','Maintenance'],
            'values' => [$available, $borrowed, $maintenance]
        ];

        $data = array(
            'title' => 'TW32 App - Welcome',
            'name' => 'JayCee',
            'equipment_total' => $total,
            'equipment_available' => $available,
            'equipment_borrowed' => $borrowed,
            'equipment_maintenance' => $maintenance,
            'pending_returns' => $pendingReturns,
            'overdue_percent' => $overduePercent,
            'recent_activity' => $recent,
            'chart_data' => $chartData,
        );

        return view('include\head_view', $data)
            .view('include\nav_view')
            .view('main_view', $data)
            .view('include\foot_view');
    }
}
?>