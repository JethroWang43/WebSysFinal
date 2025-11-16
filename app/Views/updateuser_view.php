<header class="text-center">
    <h1>Edit User Account</h1>
</header>
<main>
    <div class="col col-md-5 mx-auto">
        <form action="<?= base_url('users/update/'.$user['id']) ?>" method="post">
            <div class="form-group mb-2">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= $user['username'] ?>">
            </div>
            <div class="form-group mb-2">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="form-group mb-2">
                <label for="confirmpassword" class="form-label">Confirm Password</label>
                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control">
            </div>
            <div class="form-group mb-2">
                <label for="fullname" class="form-label">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="form-control" value="<?= $user['fullname'] ?>">
            </div>
            <div class="form-group mb-2">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $user['email'] ?>">
            </div>
            <div class="form-group mb-2">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" name="id_number" id="id_number" class="form-control" value="<?= isset($user['id_number']) ? esc($user['id_number']) : '' ?>" placeholder="202311220">
            </div>
            <div class="form-group mb-2">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="student" <?= (isset($user['role']) && $user['role']==='student') ? 'selected' : '' ?>>Student</option>
                    <option value="teacher" <?= (isset($user['role']) && $user['role']==='teacher') ? 'selected' : '' ?>>Teacher</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="<?= base_url('users'); ?>" class="btn btn-warning">Back</a></div>
        </form>
    </div>
</main>