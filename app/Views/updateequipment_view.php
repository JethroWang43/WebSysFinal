<div class="py-4">
    <h2>Edit Equipment</h2>

    <form method="post" action="<?= base_url('equipment/update/'.$item['id']) ?>">
            <?php if (session('info')): ?>
                <div class="alert alert-info"><?= session('info') ?></div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
                <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Equipment ID</label>
            <input class="form-control" name="equipment_id" value="<?= esc($item['equipment_id']) ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="<?= esc($item['name']) ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <input class="form-control" name="description" value="<?= esc($item['description']) ?>" />
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category">
                <?php $cat = $item['category'] ?? ''; ?>
                <option value="laptops" <?= $cat==='laptops' ? 'selected' : '' ?>>Laptops (with charger)</option>
                <option value="dlp" <?= $cat==='dlp' ? 'selected' : '' ?>>DLP (with extension cord, VGA/HDMI cable, power cable)</option>
                <option value="hdmi_cable" <?= $cat==='hdmi_cable' ? 'selected' : '' ?>>HDMI Cables</option>
                <option value="vga_cable" <?= $cat==='vga_cable' ? 'selected' : '' ?>>VGA Cables</option>
                <option value="dlp_remote" <?= $cat==='dlp_remote' ? 'selected' : '' ?>>DLP Remote Controls</option>
                <option value="keyboard_mouse" <?= $cat==='keyboard_mouse' ? 'selected' : '' ?>>Keyboards &amp; Mouse (with lightning cable for Mac lab)</option>
                <option value="wacom" <?= $cat==='wacom' ? 'selected' : '' ?>>Wacom Drawing Tablets (with pen)</option>
                <option value="speaker_sets" <?= $cat==='speaker_sets' ? 'selected' : '' ?>>Speaker Sets</option>
                <option value="webcams" <?= $cat==='webcams' ? 'selected' : '' ?>>Webcams</option>
                <option value="extension_cords" <?= $cat==='extension_cords' ? 'selected' : '' ?>>Extension Cords</option>
                <option value="cable_crimping_tools" <?= $cat==='cable_crimping_tools' ? 'selected' : '' ?>>Cable Crimping Tools</option>
                <option value="cable_testers" <?= $cat==='cable_testers' ? 'selected' : '' ?>>Cable Testers</option>
                <option value="lab_room_keys" <?= $cat==='lab_room_keys' ? 'selected' : '' ?>>Lab Room Keys</option>
                <option value="other" <?= $cat==='other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
                <option value="Available" <?= $item['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                <option value="Borrowed" <?= $item['status'] === 'Borrowed' ? 'selected' : '' ?>>Borrowed</option>
                <option value="Maintenance" <?= $item['status'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                <option value="Reserved" <?= $item['status'] === 'Reserved' ? 'selected' : '' ?>>Reserved</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Location</label>
            <input class="form-control" name="location" value="<?= esc($item['location']) ?>" />
        </div>

        <button class="btn btn-primary" type="submit">Save</button>
        <a href="<?= base_url('equipment') ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
