<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function doLogin()
    {
        // Handle login logic here
        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        // Handle logout logic here
        return redirect()->to('/auth/login');
    }
}
