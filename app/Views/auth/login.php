<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - RSD Portal</title>
    <link rel="icon" type="image/svg+xml" href="<?= base_url('assets/images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css?v=2.0') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Chatbot widget scoped styles */
        .chatbot-toggle{
            position:fixed;right:24px;bottom:24px;z-index:1100;
            width:56px;height:56px;border:none;border-radius:50%;
            background:linear-gradient(135deg,#8251f6,#a56ef8);color:#fff;
            box-shadow:0 10px 24px rgba(130,81,246,.35),0 6px 12px rgba(0,0,0,.1);
            display:flex;align-items:center;justify-content:center;cursor:pointer;
            transition:transform .15s ease, box-shadow .15s ease;
        }
        .chatbot-toggle:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(130,81,246,.4),0 8px 16px rgba(0,0,0,.12)}
        .chatbot-panel{
            position:fixed;right:24px;bottom:90px;z-index:1100;
            width:320px;max-width:calc(100vw - 32px);
            background:#101216;color:#e8e9ef;border:1px solid rgba(255,255,255,.06);
            border-radius:16px;overflow:hidden;box-shadow:0 18px 40px rgba(0,0,0,.35);
            display:none;flex-direction:column;
        }
        .chatbot-header{display:flex;align-items:center;gap:10px;padding:12px 14px;background:rgba(255,255,255,.02);border-bottom:1px solid rgba(255,255,255,.06)}
        .chatbot-header .status{font-size:12px;color:#9aa3b2}
        .chatbot-messages{padding:12px;height:280px;overflow:auto;background:linear-gradient(180deg,rgba(255,255,255,.02),transparent)}
        .chat-row{display:flex;margin:8px 0}
        .chat-row.user{justify-content:flex-end}
        .chat-bubble{max-width:78%;padding:10px 12px;border-radius:12px;font-size:13px;line-height:1.35}
        .chat-row.user .chat-bubble{background:#2a2f3a;color:#e8e9ef;border-top-right-radius:6px}
        .chat-row.bot .chat-bubble{background:#1a1e26;color:#dfe3ea;border:1px solid rgba(255,255,255,.06);border-top-left-radius:6px}
        .chat-suggestions{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
        .chat-suggestion{padding:6px 10px;border-radius:999px;border:1px solid rgba(255,255,255,.1);background:transparent;color:#c7ccdb;font-size:12px;cursor:pointer}
        .chat-suggestion:hover{background:rgba(255,255,255,.06)}
        .chatbot-input{display:flex;gap:8px;padding:10px;border-top:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.02)}
        .chatbot-input textarea{flex:1;resize:none;max-height:120px;height:40px;padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.08);background:#0e1116;color:#e8e9ef;font-family:inherit}
        .chatbot-input button{min-width:44px;border:none;border-radius:10px;background:#7a4ef2;color:#fff;cursor:pointer}
        @media (max-width: 600px){.chatbot-panel{right:12px;left:12px;width:auto}.chatbot-toggle{right:16px;bottom:16px}}
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
        <div class="gradient-orb orb-4"></div>
    </div>

    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="brand-content">
                <div class="logo-wrapper">
                    <div class="logo-animated">
                        <div class="logo-ring ring-1"></div>
                        <div class="logo-ring ring-2"></div>
                        <div class="logo-ring ring-3"></div>
                        <div class="logo-core">
                            <svg width="50" height="50" viewBox="0 0 100 100" fill="none">
                                <rect x="20" y="20" width="60" height="60" rx="12" fill="url(#logoGradient)"/>
                                <defs>
                                    <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#fece83;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#f4a261;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <h1 class="brand-title">
                    <span class="highlight">RSD</span> Recruitment Portal
                </h1>
                
                <p class="brand-subtitle">
                    Streamline your hiring process with our advanced recruitment management system
                </p>

                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h3>Secure</h3>
                            <p>End-to-end encryption</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h3>Fast</h3>
                            <p>Lightning-quick performance</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h3>24/7</h3>
                            <p>Always available support</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h3>Collaborative</h3>
                            <p>Team-based workflow</p>
                        </div>
                    </div>
                </div>

                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Active Users</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number">10K+</span>
                        <span class="stat-label">Applications</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Uptime</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="form-container">
                <div class="form-header">
                    <h2>Welcome Back!</h2>
                    <p>Sign in to access your dashboard</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/doLogin') ?>" method="POST" class="login-form">
                    <?= csrf_field() ?>
                    
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                    <polyline points="22,6 12,13 2,6"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" placeholder="your.email@example.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                            <button type="button" class="toggle-password" onclick="togglePassword()" tabindex="-1">
                                <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="eye-off-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-label">Remember me</span>
                        </label>
                        <a href="#" class="forgot-link">Forgot password?</a>
                    </div>

                    <button type="submit" class="submit-btn">
                        <span>Sign In</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>

                    
                </form>
            </div>

            <div class="form-footer">
                <p>&copy; 2025 RSD Portal. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Chatbot Floating Widget -->
    <button class="chatbot-toggle" id="chatbotToggle" aria-label="Open help chat" title="Quick help">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V5a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
        </svg>
    </button>
    <div class="chatbot-panel" id="chatbotPanel" role="dialog" aria-modal="true" aria-labelledby="chatbotTitle">
        <div class="chatbot-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 15s1.5 2 4 2 4-2 4-2"/></svg>
            <div>
                <div id="chatbotTitle" style="font-weight:600">RSD Assistant</div>
                <div class="status">Login & quick help</div>
            </div>
            <button onclick="chatbotClose()" style="margin-left:auto;background:transparent;border:none;color:#c7ccdb;cursor:pointer" aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages"></div>
        <div class="chatbot-input">
            <textarea id="chatbotInput" placeholder="Ask about password reset, roles, or security…" rows="1"></textarea>
            <button id="chatbotSend" aria-label="Send">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.querySelector('.eye-icon');
            const eyeOffIcon = document.querySelector('.eye-off-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }

        // Add floating label effect
        document.querySelectorAll('.input-wrapper input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Clear application form data from localStorage on login page load
        <?php if (session()->getFlashdata('clearFormData')): ?>
            localStorage.removeItem('applicationFormData');
        <?php endif; ?>

        // Chatbot logic
        const chatbot = {
            api: '<?= base_url('api/chatbot') ?>',
            panel: document.getElementById('chatbotPanel'),
            toggle: document.getElementById('chatbotToggle'),
            messages: document.getElementById('chatbotMessages'),
            input: document.getElementById('chatbotInput'),
            sendBtn: document.getElementById('chatbotSend'),
            openedOnce: false,
        };

        function chatbotOpen(){ chatbot.panel.style.display='flex'; chatbot.input.focus(); if(!chatbot.openedOnce){ chatbotGreeting(); chatbot.openedOnce=true; } }
        function chatbotClose(){ chatbot.panel.style.display='none'; }
        chatbot.toggle.addEventListener('click', ()=>{
            const isOpen = getComputedStyle(chatbot.panel).display !== 'none';
            if(isOpen) chatbotClose(); else chatbotOpen();
        });

        chatbot.sendBtn.addEventListener('click', ()=> chatbotSend());
        chatbot.input.addEventListener('keydown', (e)=>{
            if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); chatbotSend(); }
        });

        function chatbotAppend(role, text){
            const row = document.createElement('div');
            row.className = 'chat-row '+role;
            const bubble = document.createElement('div');
            bubble.className='chat-bubble';
            bubble.textContent = text;
            row.appendChild(bubble);
            chatbot.messages.appendChild(row);
            chatbot.messages.scrollTop = chatbot.messages.scrollHeight;
        }

        function renderSuggestions(list){
            if(!list || !list.length) return;
            const wrap = document.createElement('div');
            wrap.className='chat-suggestions';
            list.slice(0,4).forEach(s=>{
                const b=document.createElement('button');
                b.className='chat-suggestion';
                b.type='button';
                b.textContent=s;
                b.addEventListener('click', ()=>{ chatbot.input.value=s; chatbotSend(); });
                wrap.appendChild(b);
            });
            chatbot.messages.appendChild(wrap);
            chatbot.messages.scrollTop = chatbot.messages.scrollHeight;
        }

        async function chatbotGreeting(){
            try{
                const res = await fetch(chatbot.api, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:''})});
                const data = await res.json();
                (data.messages||[]).forEach(m=>{ if(m.role==='bot') chatbotAppend('bot', m.text); });
                renderSuggestions(data.suggestions);
            }catch(e){ chatbotAppend('bot','Hi! You can ask me about login and password reset.'); }
        }

        async function chatbotSend(){
            const text = chatbot.input.value.trim();
            if(!text) return;
            chatbotAppend('user', text);
            chatbot.input.value='';
            try{
                const res = await fetch(chatbot.api, {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({message:text})});
                const data = await res.json();
                (data.messages||[]).forEach(m=>{ if(m.role==='bot') chatbotAppend('bot', m.text); });
                // show new suggestions if any
                // remove old suggestion groups to reduce clutter
                [...chatbot.messages.querySelectorAll('.chat-suggestions')].forEach(n=>n.remove());
                renderSuggestions(data.suggestions);
            }catch(e){ chatbotAppend('bot','Sorry, I could not reach the help service. Please try again.'); }
        }
    </script>
</body>
</html>
