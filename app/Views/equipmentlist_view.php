<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Equipment Management</h2>
        <a class="btn btn-primary" href="<?= base_url('equipment/add') ?>">+ Add Equipment</a>
    </div>

    

    <div class="card">
        <div class="card-body p-0">
            <div class="p-3">
                <form method="get" class="row g-2">
                    <div class="col-md-6">
                        <input type="search" name="q" class="form-control" placeholder="Search by ID, name, description or category" value="<?= esc($q ?? '') ?>" />
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="all">Any status</option>
                            <option value="available" <?= isset($status) && $status==='available' ? 'selected' : '' ?>>Available</option>
                            <option value="borrowed" <?= isset($status) && $status==='borrowed' ? 'selected' : '' ?>>Borrowed</option>
                            <option value="reserved" <?= isset($status) && $status==='reserved' ? 'selected' : '' ?>>Reserved</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="all">All categories</option>
                            <option value="laptops" <?= isset($category) && $category==='laptops' ? 'selected' : '' ?>>Laptops</option>
                            <option value="dlp" <?= isset($category) && $category==='dlp' ? 'selected' : '' ?>>DLP</option>
                            <option value="hdmi_cable" <?= isset($category) && $category==='hdmi_cable' ? 'selected' : '' ?>>HDMI Cables</option>
                            <option value="vga_cable" <?= isset($category) && $category==='vga_cable' ? 'selected' : '' ?>>VGA Cables</option>
                            <option value="dlp_remote" <?= isset($category) && $category==='dlp_remote' ? 'selected' : '' ?>>DLP Remotes</option>
                            <option value="keyboard_mouse" <?= isset($category) && $category==='keyboard_mouse' ? 'selected' : '' ?>>Keyboard & Mouse</option>
                            <option value="wacom" <?= isset($category) && $category==='wacom' ? 'selected' : '' ?>>Wacom Tablets</option>
                            <option value="speaker_sets" <?= isset($category) && $category==='speaker_sets' ? 'selected' : '' ?>>Speaker Sets</option>
                            <option value="webcams" <?= isset($category) && $category==='webcams' ? 'selected' : '' ?>>Webcams</option>
                            <option value="extension_cords" <?= isset($category) && $category==='extension_cords' ? 'selected' : '' ?>>Extension Cords</option>
                            <option value="cable_crimping_tools" <?= isset($category) && $category==='cable_crimping_tools' ? 'selected' : '' ?>>Cable Crimping Tools</option>
                            <option value="cable_testers" <?= isset($category) && $category==='cable_testers' ? 'selected' : '' ?>>Cable Testers</option>
                            <option value="lab_room_keys" <?= isset($category) && $category==='lab_room_keys' ? 'selected' : '' ?>>Lab Room Keys</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Equipment ID</th>
                        <th>Equipment Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center">No equipment found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><input type="checkbox" /></td>
                                <td><?= esc($item['equipment_id']) ?></td>
                                <td>
                                    <strong><?= esc($item['name']) ?></strong>
                                    <div class="text-muted small"><?= esc($item['description']) ?></div>
                                </td>
                                <td><?= esc($item['category']) ?></td>
                                <td>
                                        <?php $st = strtolower($item['status'] ?? ''); ?>
                                        <?php if ($st === 'available'): ?>
                                            <span class="badge bg-success">Available</span>
                                        <?php elseif ($st === 'borrowed'): ?>
                                            <span class="badge bg-warning text-dark">Borrowed</span>
                                        <?php elseif ($st === 'maintenance' || $st === 'reserved'): ?>
                                            <span class="badge bg-danger">Reserved</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= esc(ucfirst($item['status'] ?? 'Unknown')) ?></span>
                                        <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('equipment/view/'.$item['id']) ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="<?= base_url('equipment/edit/'.$item['id']) ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <a href="<?= base_url('equipment/delete/'.$item['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

            <nav class="mt-3">
        <ul class="pagination">
            <?php
                // Build base query preserving filters
                $baseQs = [];
                if (! empty($q)) $baseQs['q'] = $q;
                if (! empty($category) && $category !== 'all') $baseQs['category'] = $category;
                if (! empty($status) && $status !== 'all') $baseQs['status'] = $status;

                $pages = max(1, (int) ceil((($totalFiltered ?? $total) ?? 0) / ($perPage ?? 6)));
                for ($p = 1; $p <= $pages; $p++):
                    $qs = $baseQs; $qs['page'] = $p;
                    $link = base_url('equipment') . '?' . http_build_query($qs);
            ?>
                <li class="page-item <?= $p == ($page ?? 1) ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
