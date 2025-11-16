<div class="py-4">
    <h2>Reschedule Reservation</h2>

    <div class="card">
        <div class="card-body">
            <?php $r = $reservation; ?>
            <form method="post" action="<?= base_url('reservations/rescheduleSubmit') ?>">
                <input type="hidden" name="id" value="<?= esc($r['id']) ?>" />

                <div class="mb-3">
                    <label class="form-label">Equipment</label>
                    <div class="form-control-plaintext"><?= esc($equipment_label ?? ($r['equipment_id'] ?? '')) ?></div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Reserve Date</label>
                        <input type="date" name="reserve_date" class="form-control" value="<?= esc(substr($r['reserved_for'] ?? '', 0, 10)) ?>" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Reserve Time</label>
                        <input type="time" name="reserve_time" class="form-control" value="<?= esc(substr($r['reserved_for'] ?? '', 11, 5)) ?>" />
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="<?= base_url('reservations') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>