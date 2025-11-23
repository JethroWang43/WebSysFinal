<div class="py-4">
    <h2>Borrow Equipment</h2>

    <!-- Initialize validation service once -->
    <?php $validation = \Config\Services::validation(); ?>

    <!-- SUCCESS/ERROR FLASH MESSAGES -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php 
    // Display general errors (if any)
    if ($validation->getErrors()): 
    ?>
        <div class="alert alert-danger mb-3">
            <!-- Displays general errors not tied to a specific field (or all errors) -->
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('borrowing/submit') ?>">

        <div class="mb-3">
            <label class="form-label required">Equipment</label>
            <select class="form-select" name="equipment_id">
                <option value="">Select Equipment</option>
                <?php 
                $old_equipment_id = old('equipment_id', $equipment['id'] ?? ''); 
                $equipment_index = 1; // Initialize equipment counter 
                ?>
                <?php foreach ($equipment_items as $item): ?>
                    <option value="<?= esc($item['id']) ?>" <?= ($old_equipment_id == $item['id']) ? 'selected' : '' ?>>
                        <?= $equipment_index++ ?> - <?= esc($item['name']) ?> (Status:
                        <?= esc($item['status']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($validation->hasError('equipment_id')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('equipment_id')) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label required">Borrower (User ID)</label>
            <select class="form-select" name="user_id" id="user_select">
                <option value="">Select Borrower (User ID)</option>
                <?php 
                // Use old value or default to the first user if available for convenience
                $old_user_id = old('user_id', array_keys($users_meta)[0] ?? '');
                $index = 1; 
                ?>
                <?php foreach ($users_meta as $user): ?>
                    <option 
                        value="<?= esc($user['id']) ?>" 
                        data-name="<?= esc($user['name']) ?>"
                        data-idnumber="<?= esc($user['studentId'] ?? '') ?>"
                        <?= ($old_user_id == $user['id']) ? 'selected' : '' ?>
                    >
                        <?= $index++ ?> - <?= esc($user['name']) ?> (
                        <?= esc($user['studentId'] ?? 'No ID') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($validation->hasError('user_id')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('user_id')) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class=" form-label required">Borrower Name (Auto-filled)</label>
            <input class="form-control" name="borrower_name" id="borrower_name" placeholder="Auto-filled from
                selection" value="<?= old('borrower_name') ?>" readonly />
            <?php if ($validation->hasError('borrower_name')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('borrower_name')) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">ID Number (Auto-filled)</label>
            <input class="form-control" name="id_number" id="id_number" placeholder="Auto-filled from selection" value="
                <?= old('id_number') ?>" readonly />
            <?php if ($validation->hasError('id_number')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('id_number')) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label required">Due Date</label>
            <?php
            // Default to today if no old value is present
            $default_date = date('Y-m-d');
            $due_date_value = old('due_date', $default_date);
            ?>
            <input class="form-control" type="date" name="due_date" value="<?= esc($due_date_value) ?>" />
            <?php if ($validation->hasError('due_date')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('due_date')) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Location of Use</label>
            <input class="form-control" name="use_location" placeholder="e.g., Room 301, Auditorium"
                value="<?= old('use_location') ?>" />
            <?php if ($validation->hasError('use_location')): ?>
                <div class="text-danger small mt-1">
                    <?= esc($validation->getError('use_location')) ?>
                </div>
            <?php endif; ?>
        </div>

        <input type="hidden" name="borrower_email" value="<?= old('borrower_email') ?>">

        <button class="btn btn-primary" type="submit">Submit Borrow Request</button>
        <a href="<?= base_url('borrowing') ?>" class="btn btn-secondary">Cancel</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect = document.getElementById('user_select');
            const borrowerNameInput = document.getElementById('borrower_name');
            const idNumberInput = document.getElementById('id_number');

            function autoFillBorrower() {
                const selectedOption = userSelect.options[userSelect.selectedIndex];
                
                // Only clear/fill if a valid option is selected (not the 'Select Borrower' placeholder)
                if (selectedOption && selectedOption.value) {
                    borrowerNameInput.value = selectedOption.dataset.name || '';
                    idNumberInput.value = selectedOption.dataset.idnumber || '';
                } else {
                    // Clear fields if the placeholder is selected
                    borrowerNameInput.value = '';
                    idNumberInput.value = '';
                }

                const oldName = '<?= old('borrower_name') ?>';
                const oldIdNumber = '<?= old('id_number') ?>';

                if (oldName) {
                    borrowerNameInput.value = oldName;
                }
                if (oldIdNumber) {
                    idNumberInput.value = oldIdNumber;
                }
            }

            // 1. Attach listener for when user changes selection (Client-Side update)
            userSelect.addEventListener('change', autoFillBorrower);

            // 2. Call on page load to auto-fill details for the default or retained user.
            autoFillBorrower();
        });
    </script>
</div>
