<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LogoutController extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        if (session()->get('logged_in')) {
            // Remove specific session data
            session()->remove('name');
            session()->remove('email');
            session()->remove('division');
            session()->remove('role');
            session()->remove('logged_in');

            // Set success flash message
            session()->setFlashdata('success', 'Successfully logged out!');
        }

        // Redirect to login page
        return redirect()->to(base_url('login'));
    }
}
