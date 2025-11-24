<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>RSD – Human Resource Management Consultancy</title>
    <meta name="description" content="RSD Human Resource Management Consultancy – Recruitment Solutions Department." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Marcellus&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/landing.css') ?>?v=<?= file_exists(FCPATH.'assets/css/landing.css') ? filemtime(FCPATH.'assets/css/landing.css') : time() ?>">
</head>
<body class="landing-body">
    <a href="#main" class="skip-link">Skip to content</a>
    <header class="site-header" role="banner">
        <div class="nav-wrap">
            <div class="brand">
                <img src="<?= base_url('assets/images/logo.svg') ?>" alt="RSD logo" class="brand-logo" />
                <span class="logo-text">RSD</span>
            </div>
            <nav class="main-nav" aria-label="Primary">
                <ul>
                    <li><a href="#hero">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#values">Mission &amp; Vision</a></li>
                    <li><a href="#careers">Careers</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a class="btn sm outline" href="/auth/login">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section id="hero" class="hero" aria-labelledby="hero-heading">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="hero-inner">
            <h1 id="hero-heading" class="hero-title"><span class="hero-accent">RSD</span><br />Human Resource<br />Management Consultancy</h1>
            <p class="hero-tag">Recruitment Solutions Department</p>
            <a href="#contact" class="btn lg ghost">Contact Us</a>
        </div>
    </section>

    <main id="main">
        <section id="about" class="about" aria-labelledby="about-heading">
            <div class="section-wrap">
                <h2 id="about-heading" class="section-title">About Us</h2>
                <p class="lead">We unify structured hiring flows, adaptive company field configuration, and actionable 7‑day trend insights so your team can make confident decisions faster.</p>
                <div class="about-grid">
                    <div class="about-card" data-fade>
                        <h3>Clarity</h3>
                        <p>Two-stage applications reduce friction and highlight progress at every point.</p>
                    </div>
                    <div class="about-card" data-fade>
                        <h3>Adaptability</h3>
                        <p>Non-technical form builder empowers HR to evolve requirements instantly.</p>
                    </div>
                    <div class="about-card" data-fade>
                        <h3>Insight</h3>
                        <p>Rolling comparisons surface subtle bottlenecks before they hinder velocity.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="services" aria-labelledby="services-heading">
            <div class="section-wrap">
                <h2 id="services-heading" class="section-title">Services</h2>
                <div class="service-grid">
                    <article class="service" data-fade>
                        <h3>Applicant Flow Design</h3>
                        <p>Craft streamlined staged experiences that optimize completion and depth.</p>
                    </article>
                    <article class="service" data-fade>
                        <h3>Custom Field Engineering</h3>
                        <p>Configure company-specific schema without exposing complexity to end users.</p>
                    </article>
                    <article class="service" data-fade>
                        <h3>Trend Analytics</h3>
                        <p>Visual KPI deltas guide prioritization and resource balancing.</p>
                    </article>
                    <article class="service" data-fade>
                        <h3>Interview State Enablement</h3>
                        <p>Structured JSON notes power next-stage badges and scheduling signals.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="values" class="mv-block" aria-labelledby="values-heading">
            <div class="section-wrap">
                <h2 id="values-heading" class="section-title">Mission &amp; Vision</h2>
                <div class="mv-grid">
                    <div class="mv-item" data-fade>
                        <figure class="mv-figure">
                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=800&q=70" alt="Notebook and laptop workspace" />
                            <span class="mv-label">Mission</span>
                        </figure>
                        <h3>Beyond Filling Roles</h3>
                        <p>We become true partners—solving challenges, opening opportunities, and driving growth for our clients, candidates, and team.</p>
                    </div>
                    <div class="mv-item" data-fade>
                        <figure class="mv-figure">
                            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=70" alt="Team collaborating over laptop" />
                            <span class="mv-label">Vision</span>
                        </figure>
                        <h3>Regional Recruitment Standard</h3>
                        <p>To be the standard in recruitment across the Asia-Pacific—empowering businesses and people to succeed together.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="careers" class="careers" aria-labelledby="careers-heading">
            <div class="section-wrap">
                <h2 id="careers-heading" class="section-title">Careers</h2>
                <p class="lead">Join a growing ecosystem focused on clarity and velocity. We value accessibility, evidence-based iteration, and thoughtful design.</p>
                <ul class="positions" role="list">
                    <li data-fade><span class="role">Frontend Engineer</span><span class="status">Open</span></li>
                    <li data-fade><span class="role">Data Analyst</span><span class="status">Open</span></li>
                    <li data-fade><span class="role">UX Researcher</span><span class="status">Upcoming</span></li>
                </ul>
            </div>
        </section>

        <section id="contact" class="contact" aria-labelledby="contact-heading">
            <div class="section-wrap">
                <h2 id="contact-heading" class="section-title">Contact</h2>
                <form class="contact-form" action="#" method="post" novalidate>
                    <div class="form-grid">
                        <label> Name
                            <input type="text" name="name" required aria-required="true" />
                        </label>
                        <label> Email
                            <input type="email" name="email" required aria-required="true" />
                        </label>
                        <label class="full"> Message
                            <textarea name="message" rows="5" required aria-required="true"></textarea>
                        </label>
                    </div>
                    <button class="btn primary" type="submit">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="footer-wrap">
            <div class="foot-brand">RSD Consultancy</div>
            <nav aria-label="Footer" class="foot-nav">
                <a href="#hero">Home</a>
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#careers">Careers</a>
                <a href="#values">Mission &amp; Vision</a>
                <a href="#contact">Contact</a>
            </nav>
            <div class="foot-copy">&copy; <?= date('Y') ?> RSD. All rights reserved.</div>
        </div>
    </footer>

    <script>
        // Respect reduced motion preference
        if(window.matchMedia('(prefers-reduced-motion: reduce)').matches){document.documentElement.classList.add('reduced-motion');}
        // Intersection based fade-ins for scroll sections
        const observer = new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');observer.unobserve(e.target);}})},{threshold:.15});
        document.querySelectorAll('[data-fade]').forEach(el=>observer.observe(el));
        // Initial load animation trigger
        window.addEventListener('DOMContentLoaded',()=>{document.body.classList.add('ready');});
    </script>
</body>
</html>
