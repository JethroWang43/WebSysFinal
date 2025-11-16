<header class="text-center">
    <h1>Users List</h1>
</header>
<main>
    <div class="col col-md-8 mx-auto">
        <?php
        if(session('success')):
        ?>
        <div class="alert alert-success">
            <p><?= session('success') ?></p>
        </div>
        <?php
        endif;
        ?>
        <a href="<?= base_url('users/add'); ?>" class="btn btn-primary">Add New User</a>
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>User Name</th>
                    <th>Full Name</th>
                    <th>E-Mail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td><?= $user['StudentID'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['fullname'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td>
                        <a href="<?= base_url('users/view/'.$user['id']) ?>" class="btn btn-sm btn-success">View</a>
                        <a href="<?= base_url('users/edit/'.$user['id']) ?>" class="btn btn-sm btn-secondary">Update</a>
                        <a href="<?= base_url('users/delete/'.$user['id']) ?>" class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
            // Controller-side pagination (page, perPage, totalFiltered)
            $page = $page ?? 1;
            $perPage = $perPage ?? 6;
            $totalFiltered = $totalFiltered ?? count($users ?? []);
            $pages = max(1, (int) ceil($totalFiltered / $perPage));
            $cur = (int) $page;
        ?>
        <?php if ($pages > 1): ?>
            <nav class="mt-3" aria-label="Users pagination">
                <ul class="pagination">
                    <?php
                        $baseQs = $_GET;
                        for ($p = 1; $p <= $pages; $p++):
                            $qs = $baseQs; $qs['page'] = $p;
                            $link = base_url('users') . '?' . http_build_query($qs);
                    ?>
                        <li class="page-item <?= $p == $cur ? 'active' : '' ?>"><a class="page-link" href="<?= $link ?>"><?= $p ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>