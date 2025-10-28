<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to($this->getDashboardRoute());
        }
        
        return view('auth/login');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function doLogin()
    {
        $userModel = new UserModel();
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');
        
        // Validate input
        if (!$email || !$password) {
            return redirect()->back()->with('error', 'Please fill in all fields');
        }
        
        // Verify credentials
        $user = $userModel->verifyPassword($email, $password);
        
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'Your account is inactive. Please contact administrator.');
        }
        
        // Set session data
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'user_type' => $user['user_type'],
            'isLoggedIn' => true,
        ];
        
        session()->set($sessionData);
        
        // Update last login
        $userModel->updateLastLogin($user['id']);
        
        // Redirect to admin dashboard
        return redirect()->to('/admin/dashboard')->with('success', 'Welcome back!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully');
    }
    
    private function getDashboardRoute()
    {
        return '/admin/dashboard';
    }
}
