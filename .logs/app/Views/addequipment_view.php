<div class="py-4">
	<h2>Add Equipment</h2>

	<form method="post" action="<?= base_url('equipment/insert') ?>">
		<div class="mb-3">
			<label class="form-label">Equipment ID (optional)</label>
			<input class="form-control" name="equipment_id" placeholder="EQ-XXX or leave blank to auto-generate" />
		</div>
		<div class="mb-3">
			<label class="form-label">Name</label>
			<input class="form-control" name="name" />
		</div>
		<div class="mb-3">
			<label class="form-label">Description</label>
			<input class="form-control" name="description" />
		</div>
		<div class="mb-3">
			<label class="form-label">Category</label>
			<select class="form-select" name="category">
				<option value="laptops">Laptops (with charger)</option>
				<option value="dlp">DLP (with extension cord, VGA/HDMI cable, power cable)</option>
				<option value="hdmi_cable">HDMI Cables</option>
				<option value="vga_cable">VGA Cables</option>
				<option value="dlp_remote">DLP Remote Controls</option>
				<option value="keyboard_mouse">Keyboards &amp; Mouse (with lightning cable for Mac lab)</option>
				<option value="wacom">Wacom Drawing Tablets (with pen)</option>
				<option value="speaker_sets">Speaker Sets</option>
				<option value="webcams">Webcams</option>
				<option value="extension_cords">Extension Cords</option>
				<option value="cable_crimping_tools">Cable Crimping Tools</option>
				<option value="cable_testers">Cable Testers</option>
				<option value="lab_room_keys">Lab Room Keys</option>
				<option value="other">Other</option>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label">Status</label>
			<select class="form-select" name="status">
				<option>Available</option>
				<option>Borrowed</option>
				<option>Maintenance</option>
				<option>Reserved</option>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label">Location</label>
			<input class="form-control" name="location" value="ITSO" />
		</div>

		<button class="btn btn-primary" type="submit">Add Equipment</button>
		<a href="<?= base_url('equipment') ?>" class="btn btn-secondary">Cancel</a>
	</form>
</div>
