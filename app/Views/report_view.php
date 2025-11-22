<div class="container-fluid pt-4">
    <h3 class="mt-4">Equipment Status Breakdown</h3>
    <ul class="list-group">
        <?php foreach ($equipment_status as $status => $count): ?>
            <a href="<?= base_url('equipment?status=' . urlencode($status)) ?>"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <?= esc($status) ?>
                <span class="badge bg-primary rounded-pill"><?= $count ?></span>
            </a>
        <?php endforeach; ?>
    </ul>
</div>
</div>

</div>
