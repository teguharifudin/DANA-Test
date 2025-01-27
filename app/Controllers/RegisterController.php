<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class RegisterController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->helpers = ['form', 'url'];
    }

    public function index()
    {
        $data = [
            'title' => 'Register | DANA TEST',
            'divisions' => [
                'IT' => 'Information Technology',
                'HR' => 'Human Resources',
                'Finance' => 'Finance',
                'Marketing' => 'Marketing',
                'Operations' => 'Operations',
                'Sales' => 'Sales',
                'Other' => 'Other'
            ],
            'roles' => [
                'requestor' => 'Requestor',
                'procurement' => 'Procurement',
                'manager' => 'Line Manager',
                'management' => 'Excom I',
                'executive' => 'Excom II'
            ],
            'departments' => [
                'Business' => 'Business',
                'General' => 'General',
                'Operational' => 'Operational'
            ],
            'divisions' => [
                'IT' => 'Information Technology',
                'HR' => 'Human Resources',
                'Finance' => 'Finance',
                'Marketing' => 'Marketing',
                'Operations' => 'Operations',
                'Sales' => 'Sales',
                'Other' => 'Other'
            ],
        ];

        return view('auth/register', $data);
    }

    public function store()
    {
        $data = $this->request->getPost([
            'name',
            'email',
            'password',
            'role',
            'department',
            'division'
        ]);

        // echo $data['role'];
        // echo $data['department'];
        // echo $data['division'];
        // die('e');

        if (!$this->validateData($data, $this->model->validationRules)) {
            return $this->index();
        }

        $user = $this->validator->getValidated();

        $save = $this->model->save($user);

        if ($save) {
            session()->setFlashdata('success', 'Registration Successful!');
            return redirect()->to(base_url('login'));
        } else {
            session()->setFlashdata('error', $this->model->errors());
            return redirect()->back();
        }
    }
}
