<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // If arguments (roles) are provided, check user's role
        if (!empty($arguments)) {
            $userRole = session()->get('role');
            
            // Check if user's role is in the allowed roles
            if (!in_array($userRole, $arguments)) {
                return redirect()->to(base_url('dashboard'))
                    ->with('error', 'You do not have permission to access this page');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
