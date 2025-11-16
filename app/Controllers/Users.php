<?php
namespace App\Controllers;

class Users extends BaseController {
    public function index() {
        $usermodel = model('Users_model');
        $session = session();
        // controller-side pagination (consistent with Borrowing/Reservations)
        $perPage = 6;
        $page = (int) ($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;

        // Try to load users from DB; fall back to empty array on error
        try {
            $all = $usermodel->findAll();
        } catch (\Throwable $e) {
            $all = [];
        }

        $all = array_values($all);
        $totalFiltered = count($all);
        $pages = max(1, (int) ceil($totalFiltered / $perPage));
        if ($page > $pages) $page = $pages;
        $offset = ($page - 1) * $perPage;
        $paged = array_slice($all, $offset, $perPage);

        $data = array(
            'title' => 'TW32 App - Welcome',
            'users' => $paged,
            'page' => $page,
            'perPage' => $perPage,
            'totalFiltered' => $totalFiltered,
        );

        return view('include\\head_view', $data)
            .view('include\\nav_view')
            .view('userslist_view', $data)
            .view('include\\foot_view');
    }

    public function add() {
        $session = session();
        $data = array(
            'title' => 'TW32 App - Add New User',
        );

        return view('include\head_view', $data)
            .view('include\nav_view')
            .view('adduser_view')
            .view('include\foot_view');
    }

    public function insert() {
        $usermodel = model('Users_model');
        // Creates the session object
        $session = session(); // $session = service('session');

        // Creates and loads the Validation library
        $validation = service('validation');

        $data = array (
            'StudentID'=> $this->request->getPost('StudentID'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'confirmpassword' => $this->request->getPost('confirmpassword'),
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email'),
        );

        // Runs the validation
        if(! $validation->run($data, 'signup')){
            // If validation fails, reload the form passing the error messages
            $data = array(
                'title' => 'TW32 App - Add New User',
                // 'errors' => $validation->getErrors()
            );
            // Set the flash data session item for the errors
            $session->setFlashData('errors', $validation->getErrors());

            // return view('include\head_view', $data)
            //     .view('include\nav_view')
            //     .view('adduser_view', $data)
            //     .view('include\foot_view');
            return redirect()->to('users/add');
        }

        $usermodel->insert($data);

        // store extra fields (role and id_number) in session so we don't require DB schema changes
        $id_number = $this->request->getPost('id_number');
        $role = $this->request->getPost('role');

        // try to find the created user to get its id
        $created = $usermodel->where('username', $data['username'])->first();
        if ($created && isset($created['id'])) {
            $uid = $created['id'];
            $extras = $session->get('user_extras') ?? [];
            $extras[$uid] = ['id_number' => $id_number, 'role' => $role];
            $session->set('user_extras', $extras);
        }

        $session->setFlashData('success', 'Adding new user is successful.');

        return redirect()->to('users');
    }

    public function view($id) {
        $usermodel = model('Users_model');

        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($id)
        );

        return view('include\head_view', $data)
            .view('include\nav_view')
            .view('viewuser_view', $data)
            .view('include\foot_view');
    }

    public function edit($id) {
        $usermodel = model('Users_model');
        $session = session();

        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($id)
        );

        return view('include\head_view', $data)
            .view('include\nav_view')
            .view('updateuser_view', $data)
            .view('include\foot_view');
    }

    public function update($id) {
        $usermodel = model('Users_model');
        $session = session();

        $data = array (
            'StudentID' => $this->request->getPost('StudentID'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'fullname' => $this->request->getPost('fullname'),
            'email' => $this->request->getPost('email'),
        );

        $usermodel->update($id, $data);

        // store extra fields in session
        $id_number = $this->request->getPost('id_number');
        $role = $this->request->getPost('role');
        $extras = $session->get('user_extras') ?? [];
        $extras[$id] = ['id_number' => $id_number, 'role' => $role];
        $session->set('user_extras', $extras);

        return redirect()->to('users');
    }

    public function delete($id) {
        $usermodel = model('Users_model');
        $session = session();
        $usermodel->delete($id);
        return redirect()->to('users');
    }
}
?>