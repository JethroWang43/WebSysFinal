<header class="text-center">
    <h1>Add New User</h1>
</header>
<main>
    <div class="col col-md-5 mx-auto">
        <?php
        if(session('errors')):
        ?>
        <div class="alert alert-danger">
            <p><?= implode('<br>', session('errors')) ?></p>
        </div>
        <?php
        endif;
        ?>
        <form action="<?= base_url('users/insert') ?>" method="post" novalidate>
            <div class="form-group mb-2">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= set_value('username') ?>">
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
                <input type="text" name="fullname" id="fullname" class="form-control">
            </div>
            <div class="form-group mb-2">
                <label for="StudentID" class="form-label">ID Number</label>
                <input type="text" name="StudentID" id="StudentID" class="form-control" placeholder="202311220">
            </div>
            <div class="form-group mb-2">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select">
                    <option value="itso_personnel">ITSO Personnel</option>
                    <option value="associate">Associate</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="<?= base_url('users'); ?>" class="btn btn-warning">Cancel</a></div>
        </form>
    </div>
</main>