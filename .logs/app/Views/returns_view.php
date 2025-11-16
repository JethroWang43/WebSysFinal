<div class="py-4">
    <h2>Return Equipment</h2>

    <div class="card">
        <div class="card-body">
            <?php if (empty($borrows)): ?>
                <div class="p-4"><p class="lead">No active borrows to return.</p></div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr><th>#</th><th>Ref</th><th>Equipment</th><th>Location</th><th>Borrower</th><th>Borrowed</th><th>Due</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrows as $b): ?>
                            <tr>
                                <td><?= $b['id'] ?></td>
                                <td><?= esc($b['ref'] ?? '') ?></td>
                                <td>
                                    <?php
                                        $equipLabel = esc($b['equipment_id']);
                                        if (! empty($equipment_items) && isset($equipment_items[$b['equipment_id']])) {
                                            $ei = $equipment_items[$b['equipment_id']];
                                            $equipLabel = esc($ei['name']) . ' <small class="text-muted">(' . esc($ei['equipment_id'] ?? '') . ')</small>';
                                        }
                                    ?>
                                    <?= $equipLabel ?>
                                </td>
                                <td>
                                    <?php
                                        $equipLocation = '';
                                        if (! empty($equipment_items) && isset($equipment_items[$b['equipment_id']])) {
                                            $equipLocation = esc($equipment_items[$b['equipment_id']]['location'] ?? '');
                                        }
                                    ?>
                                    <?= $equipLocation ?>
                                </td>
                                <td><?= esc($b['borrower_name'] ?: $b['user_id']) ?> <div class="text-muted small"><?= esc($b['id_number'] ?? '') ?></div></td>
                                <td><?= esc($b['date_borrowed']) ?></td>
                                <td><?= esc($b['due_date'] ?? '') ?></td>
                                <td>
                                    <a href="<?= base_url('borrowing/return/'.$b['id']) ?>" class="btn btn-sm btn-success" onclick="return confirm('Mark this item as returned?')">Return</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
