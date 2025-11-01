<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SystemLogModel;

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
            // Log failed login attempt
            $systemLog = new SystemLogModel();
            $systemLog->logActivity(
                'Failed Login',
                'auth',
                'Failed login attempt for email: ' . $email,
                null
            );
            
            return redirect()->back()->with('error', 'Invalid email or password');
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            // Log inactive account access attempt
            $systemLog = new SystemLogModel();
            $systemLog->logActivity(
                'Inactive Account Login Attempt',
                'auth',
                'Login attempt on inactive account: ' . $email,
                $user['id']
            );
            
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
            'justLoggedIn' => true,  // Flag for clearing form data
        ];
        
        session()->set($sessionData);
        
        // Update last login
        $userModel->updateLastLogin($user['id']);
        
        // Log successful login
        $systemLog = new SystemLogModel();
        $systemLog->logActivity(
            'Login',
            'auth',
            'User logged in successfully',
            $user['id']
        );
        
        // Redirect based on user type
        $redirectUrl = $this->getDashboardRoute($user['user_type']);
        return redirect()->to($redirectUrl)->with('success', 'Welcome back!');
    }

    public function logout()
    {
        // Get user ID before destroying session
        $userId = session()->get('user_id');
        
        // Log logout
        if ($userId) {
            $systemLog = new SystemLogModel();
            $systemLog->logActivity(
                'Logout',
                'auth',
                'User logged out',
                $userId
            );
        }
        
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully')->with('clearFormData', true);
    }
    
    private function getDashboardRoute($userType)
    {
        switch ($userType) {
            case 'admin':
                return '/admin/dashboard';
            case 'interviewer':
                return '/interviewer/dashboard';
            case 'applicant':
                return '/applicant/dashboard';
            default:
                return '/admin/dashboard';
        }
    }
}
