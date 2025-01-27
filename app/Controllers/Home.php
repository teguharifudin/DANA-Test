<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Home | DANA TEST'
        ];

        return view('home', $data);
    }
}
