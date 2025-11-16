<div class="py-4">
    <h2>Borrow Equipment</h2>

    <?php if (empty($equipment) && ! empty($equipment_items)): ?>
        <div class="mb-3">
            <label class="form-label">Select Equipment</label>
            <select name="equipment_id" class="form-select" required>
                <option value="">-- Select equipment --</option>
                <?php foreach ($equipment_items as $eid => $ei): ?>
                    <option value="<?= esc($ei['id']) ?>" <?= (isset($equipmentId) && $equipmentId == $ei['id']) ? 'selected' : '' ?>><?= esc($ei['name']) ?> (<?= esc($ei['equipment_id'] ?? '') ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php else: ?>
        <div class="card p-3 mb-3">
            <h5><?= esc($equipment['name'] ?? 'Selected item') ?> <small class="text-muted"><?= esc($equipment['equipment_id'] ?? '') ?></small></h5>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('borrowing/submit') ?>">
        <?php if (! (empty($equipment) && ! empty($equipment_items))): ?>
            <input type="hidden" name="equipment_id" value="<?= esc($equipmentId) ?>" />
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Borrower (choose existing user)</label>
            <select name="user_id" class="form-select">
                <option value="">-- Select user --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= esc($u['fullname'] ?: $u['username']) ?> (<?= esc($u['email']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">Or enter borrower details manually</div>
        <div class="mb-3">
            <label class="form-label">Borrower Name</label>
            <input class="form-control" name="borrower_name" id="borrower_name" list="borrower_students" autocomplete="off" />
            <datalist id="borrower_students">
                <?php foreach ($users as $u): ?>
                    <option value="<?= esc($u['fullname'] ?: $u['username']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div class="mb-3">
            <label class="form-label">Email (optional)</label>
            <input class="form-control" name="borrower_email" placeholder="name@example.com" />
        </div>
        <div class="mb-3">
            <label class="form-label">ID Number</label>
            <input class="form-control" name="id_number" id="borrower_id_number" placeholder="202311220" />
        </div>
        <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input class="form-control" type="date" name="due_date" />
        </div>

        <div class="mb-3">
            <label class="form-label">Bring To (Location / Room)</label>
            <input class="form-control" name="use_location" placeholder="e.g. Lab A - Room 101" />
        </div>

        <button class="btn btn-primary" type="submit">Confirm Borrow</button>
        <a class="btn btn-secondary" href="<?= base_url('equipment') ?>">Cancel</a>
    </form>
</div>

<script>
// Server-provided users_meta includes `studentId` from DB or session fallback
const borrowUsersMeta = <?= json_encode($users_meta ?? []) ?>;

function borrowFindUserIdByName(name) {
    name = (name || '').toLowerCase().trim();
    for (const k in borrowUsersMeta) {
        if ((borrowUsersMeta[k].name || '').toLowerCase() === name) return borrowUsersMeta[k].id;
    }
    return null;
}

document.querySelector('select[name="user_id"]')?.addEventListener('change', function() {
    const uid = this.value;
    if (! uid) return;
    const meta = borrowUsersMeta[uid] || {};
    document.getElementById('borrower_name').value = meta.name || '';
    document.getElementById('borrower_id_number').value = meta.studentId || '';
});

document.getElementById('borrower_name')?.addEventListener('input', function() {
    const val = this.value;
    const uid = borrowFindUserIdByName(val);
    if (uid) {
        const meta = borrowUsersMeta[uid] || {};
        document.getElementById('borrower_id_number').value = meta.studentId || '';
        const sel = document.querySelector('select[name="user_id"]');
        if (sel) sel.value = uid;
    }
});
</script>
</div>
