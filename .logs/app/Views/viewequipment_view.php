<div class="py-4">
    <h2>View Equipment</h2>

    <div class="card p-3">
        <h4><?= esc($item['name']) ?> <small class="text-muted"><?= esc($item['equipment_id']) ?></small></h4>
        <p><?= esc($item['description']) ?></p>

        <dl class="row">
            <dt class="col-sm-3">Category</dt>
            <dd class="col-sm-9"><?= esc($item['category']) ?></dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9"><?= esc($item['status']) ?></dd>

            <dt class="col-sm-3">Location</dt>
            <dd class="col-sm-9"><?= esc($item['location']) ?></dd>

            <dt class="col-sm-3">Last Updated</dt>
            <dd class="col-sm-9"><?= esc($item['last_updated']) ?></dd>
        </dl>

        <a href="<?= base_url('equipment/edit/'.$item['id']) ?>" class="btn btn-secondary">Edit</a>
        <a href="<?= base_url('equipment') ?>" class="btn btn-outline-primary">Back</a>
    </div>
</div>
