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
            // Pass the current session user_type to avoid ArgumentCountError
            $userType = session()->get('user_type');
            return redirect()->to($this->getDashboardRoute($userType));
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
        
        // Trace start of login for diagnostics
        if (function_exists('log_message')) {
            log_message('info', 'Auth::doLogin start email={email} ip={ip}', [
                'email' => (string) $email,
                'ip'    => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ]);
        }
        
        // Validate input
        if (!$email || !$password) {
            if (function_exists('log_message')) {
                log_message('warning', 'Auth::doLogin missing fields email_present={e} password_present={p}', [
                    'e' => $email ? 'y' : 'n',
                    'p' => $password ? 'y' : 'n',
                ]);
            }
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
            if (function_exists('log_message')) {
                log_message('warning', 'Auth::doLogin invalid credentials for {email}', ['email' => (string) $email]);
            }
            
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
            if (function_exists('log_message')) {
                log_message('info', 'Auth::doLogin inactive account id={id} email={email}', [
                    'id' => (string) $user['id'],
                    'email' => (string) $email,
                ]);
            }
            
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
        // Regenerate session ID to prevent fixation
        if (function_exists('session')) {
            @session()->regenerate(true);
        }
        if (function_exists('log_message')) {
            log_message('info', 'Auth::doLogin success id={id} type={type}', [
                'id' => (string) $user['id'],
                'type' => (string) $user['user_type'],
            ]);
        }
        
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
        if (function_exists('log_message')) {
            log_message('info', 'Auth::doLogin redirecting to {url}', ['url' => (string) $redirectUrl]);
        }
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
    
    private function getDashboardRoute($userType = null)
    {
        // Fallback to session if not provided
        if ($userType === null) {
            $userType = session()->get('user_type');
        }
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
