<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RSD Portal</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="back-button">
                <a href="<?= base_url() ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
            </div>
            
            <div class="login-form-container">
                <div class="login-header">
                    <h1>Welcome Back</h1>
                    <p>Please enter your credentials to continue</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/doLogin') ?>" method="post" class="login-form">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input type="email" id="email" name="email" placeholder="e.g. user@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <input type="password" id="password" name="password" placeholder="At least 8 characters" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="<?= base_url('auth/forgot-password') ?>" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="login-button">Sign In</button>

                    <div class="register-link">
                        Don't have an account? <a href="<?= base_url('auth/register') ?>">Create one</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="right-section">
            <div class="brand-container">
                <div class="brand-logo">
                    <svg width="80" height="80" viewBox="0 0 200 200" fill="none">
                        <!-- Outer circles (gray layers) -->
                        <circle cx="100" cy="100" r="95" fill="#c5c5c5" opacity="0.4"/>
                        <circle cx="100" cy="85" r="75" fill="#b5b5b5" opacity="0.5"/>
                        <circle cx="85" cy="100" r="70" fill="#a5a5a5" opacity="0.5"/>
                        <circle cx="115" cy="100" r="70" fill="#a5a5a5" opacity="0.5"/>
                        <circle cx="100" cy="115" r="75" fill="#b5b5b5" opacity="0.5"/>
                        <!-- Center square (orange/gold) -->
                        <rect x="70" y="70" width="60" height="60" rx="8" fill="#fece83"/>
                        <!-- Add some depth to center -->
                        <rect x="75" y="75" width="50" height="50" rx="6" fill="#f4a261" opacity="0.3"/>
                    </svg>
                </div>
                <h2>RSD <span>Portal</span></h2>
                <div class="features">
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Secure & Encrypted</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Fast & Responsive</span>
                    </div>
                    <div class="feature-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>24/7 Support Available</span>
                    </div>
                </div>
            </div>
            <div class="decorative-circles">
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                <div class="circle circle-3"></div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html>
