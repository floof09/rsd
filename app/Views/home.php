<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RSD Portal – Talent Acceleration</title>
  <meta name="description" content="RSD Portal: modern hiring flow, staged applications, real-time insights." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/home.css') ?>?v=<?= file_exists(FCPATH.'assets/css/home.css') ? filemtime(FCPATH.'assets/css/home.css') : time() ?>">
</head>
<body class="home-body">
  <a href="#main" class="skip-link">Skip to content</a>
  <header class="site-header" role="banner">
    <div class="wrap header-flex">
      <div class="brand">
        <span class="logo-mark" aria-hidden="true">★</span>
        <span class="logo-text">RSD Portal</span>
      </div>
      <nav aria-label="Primary" class="main-nav">
        <ul>
          <li><a href="#solutions">Solutions</a></li>
          <li><a href="#process">Process</a></li>
          <li><a href="#insights">Insights</a></li>
          <li><a href="#articles">Articles</a></li>
          <li><a class="btn sm primary" href="/auth/login">Login</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero" id="hero" aria-labelledby="hero-heading">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="wrap hero-inner">
      <h1 id="hero-heading" class="hero-title">Accelerate <span class="accent">Hiring</span> With Clarity</h1>
      <p class="hero-sub">Staged applications, trend-aware dashboards, and adaptive forms—designed for lean teams scaling smart.</p>
      <div class="hero-cta">
        <a href="/auth/login" class="btn xl primary">Enter Portal</a>
        <a href="#solutions" class="btn xl outline">Explore</a>
      </div>
      <div class="hero-stats" aria-label="Platform highlights">
        <div class="stat" data-fade><span class="value">2x</span><span class="label">Review Speed</span></div>
        <div class="stat" data-fade><span class="value">99%</span><span class="label">Uptime</span></div>
        <div class="stat" data-fade><span class="value">Adaptive</span><span class="label">Company Fields</span></div>
      </div>
      <div class="dual-intent" data-fade>
        <button class="intent-btn active" data-target="hire" aria-pressed="true">I'm Hiring</button>
        <button class="intent-btn" data-target="apply" aria-pressed="false">I'm Applying</button>
      </div>
      <div class="intent-panels" aria-live="polite">
        <div class="intent-panel show" id="panel-hire">
          <p>Access structured hiring flow, configure company fields, and monitor daily trends in one unified portal.</p>
        </div>
        <div class="intent-panel" id="panel-apply">
          <p>Submit a concise initial application then enrich details only when relevant—transparent progress each step.</p>
        </div>
      </div>
    </div>
  </section>

  <main id="main">
    <!-- SOLUTIONS / SERVICES STYLE GRID -->
    <section id="solutions" class="solutions" aria-labelledby="solutions-heading">
      <div class="wrap">
        <h2 id="solutions-heading" class="section-title">Platform Pillars</h2>
        <div class="solutions-grid">
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Two-Stage Applications</h3>
            <p>Frictionless intake; deeper company-specific data captured only when needed.</p>
          </article>
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Real Trends</h3>
            <p>Rolling 7-day analytics surface momentum & bottlenecks before they grow.</p>
          </article>
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Adaptive Builder</h3>
            <p>Non-technical field creation with auto key slug & reorder simplicity.</p>
          </article>
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Unified Palette</h3>
            <p>Gold & neutral consistency reinforces brand and reduces cognitive load.</p>
          </article>
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Chat Guidance</h3>
            <p>Embedded assistant answers onboarding questions instantly at login.</p>
          </article>
          <article class="solution" data-fade>
            <div class="sol-img" aria-hidden="true"></div>
            <h3>Structured Notes</h3>
            <p>JSON-based storage enables upcoming interview state & feature flags.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- PROCESS TIMELINE -->
    <section id="process" class="process" aria-labelledby="process-heading">
      <div class="wrap process-flex">
        <div class="process-copy">
          <h2 id="process-heading" class="section-title">Structured Flow</h2>
          <ol class="steps">
            <li><strong>Intake:</strong> Candidate provides essentials rapidly.</li>
            <li><strong>Company Fields:</strong> Relevant detail captured contextually.</li>
            <li><strong>Interview Stage:</strong> Badge & scheduling preparation.</li>
            <li><strong>Decision & Metrics:</strong> Trends inform acceptance.</li>
          </ol>
          <a href="/auth/login" class="btn primary">Get Started</a>
        </div>
        <div class="process-diagram" aria-hidden="true">
          <div class="node">Intake</div>
          <div class="node">Company</div>
          <div class="node">Interview</div>
          <div class="node">Decision</div>
        </div>
      </div>
    </section>

    <!-- INSIGHTS / VALUE ICONS -->
    <section id="insights" class="insights" aria-labelledby="insights-heading">
      <div class="wrap">
        <h2 id="insights-heading" class="section-title">Value Drivers</h2>
        <div class="insight-grid">
          <div class="insight" data-fade>
            <div class="icon" aria-hidden="true">📊</div>
            <h3>Predictive Cues</h3>
            <p>Micro-trends highlight emerging delays before they cost throughput.</p>
          </div>
          <div class="insight" data-fade>
            <div class="icon" aria-hidden="true">🛠️</div>
            <h3>Low Overhead</h3>
            <p>Lean asset strategy & deterministic cache busting keep deploys fast.</p>
          </div>
          <div class="insight" data-fade>
            <div class="icon" aria-hidden="true">🔒</div>
            <h3>Future-Ready</h3>
            <p>JSON structured notes simplify upcoming interview state features.</p>
          </div>
          <div class="insight" data-fade>
            <div class="icon" aria-hidden="true">⚡</div>
            <h3>Focused Velocity</h3>
            <p>Interface minimizes distraction so reviewers concentrate on decisions.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS SLIDER -->
    <section id="testimonials" class="testimonials" aria-labelledby="testimonials-heading">
      <div class="wrap">
        <h2 id="testimonials-heading" class="section-title">Client Confidence</h2>
        <div class="slider" role="region" aria-label="Testimonials carousel">
          <button class="slide-nav prev" aria-label="Previous testimonial">←</button>
          <div class="slides">
            <figure class="slide current" data-index="0">
              <blockquote>“The staged flow reduced drop-offs and clarified ownership for our hiring team.”</blockquote>
              <figcaption>Operations Lead – Tech Growth</figcaption>
            </figure>
            <figure class="slide" data-index="1">
              <blockquote>“Trend views helped us spot an early review backlog and correct it in hours.”</blockquote>
              <figcaption>Recruitment Manager – SaaS</figcaption>
            </figure>
            <figure class="slide" data-index="2">
              <blockquote>“Field builder made custom data collection painless for non-engineers.”</blockquote>
              <figcaption>HR Director – Enterprise</figcaption>
            </figure>
          </div>
          <button class="slide-nav next" aria-label="Next testimonial">→</button>
        </div>
        <div class="dots" aria-hidden="true"></div>
      </div>
    </section>

    <!-- ARTICLES PREVIEW (STATIC PLACEHOLDER) -->
    <section id="articles" class="articles" aria-labelledby="articles-heading">
      <div class="wrap">
        <h2 id="articles-heading" class="section-title">Recent Notes & Advice</h2>
        <div class="article-grid">
          <article class="article" data-fade>
            <h3>Designing Two-Stage Hiring</h3>
            <p>Patterns for reducing applicant drop-off while retaining depth.</p>
            <a href="#" class="read">Read →</a>
          </article>
          <article class="article" data-fade>
            <h3>Trend Windows Explained</h3>
            <p>Why comparing rolling 7-day slices surfaces early signals.</p>
            <a href="#" class="read">Read →</a>
          </article>
          <article class="article" data-fade>
            <h3>Field Builder Usability</h3>
            <p>Transforming technical schema editing into guided configuration.</p>
            <a href="#" class="read">Read →</a>
          </article>
          <article class="article" data-fade>
            <h3>Color Consistency Payoff</h3>
            <p>How unified palette reduces decision fatigue & context switching.</p>
            <a href="#" class="read">Read →</a>
          </article>
          <article class="article" data-fade>
            <h3>Interview State Badges</h3>
            <p>Designing subtle status indicators that remain accessible.</p>
            <a href="#" class="read">Read →</a>
          </article>
        </div>
      </div>
    </section>

    <!-- FAQ ACCORDION -->
    <section id="faq" class="faq" aria-labelledby="faq-heading">
      <div class="wrap">
        <h2 id="faq-heading" class="section-title">Frequently Asked Questions</h2>
        <div class="faq-list" role="list">
          <div class="faq-item" data-fade>
            <button class="faq-q" aria-expanded="false">How do staged applications reduce friction?</button>
            <div class="faq-a" hidden>Applicants only provide essentials first; deeper fields appear contextually, lowering early abandonment.</div>
          </div>
          <div class="faq-item" data-fade>
            <button class="faq-q" aria-expanded="false">Can non-technical staff edit company fields?</button>
            <div class="faq-a" hidden>Yes. Builder auto-generates keys and hides advanced validation behind a collapsible panel.</div>
          </div>
          <div class="faq-item" data-fade>
            <button class="faq-q" aria-expanded="false">What powers trend comparisons?</button>
            <div class="faq-a" hidden>Rolling 7-day windows contrasted with the previous 7 days to spot directional change early.</div>
          </div>
          <div class="faq-item" data-fade>
            <button class="faq-q" aria-expanded="false">Is the palette accessible?</button>
            <div class="faq-a" hidden>Gold accents pair with deep ink for sufficient contrast; neutrals reserved for low-emphasis surfaces.</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta" aria-labelledby="cta-heading">
      <div class="wrap cta-inner">
        <h2 id="cta-heading">Ready To Streamline Hiring?</h2>
        <p>Enter the portal and experience structured velocity.</p>
        <a href="/auth/login" class="btn xl primary">Login</a>
      </div>
    </section>
  </main>

  <footer class="site-footer" role="contentinfo">
    <div class="wrap footer-flex">
      <div class="foot-brand">RSD Portal</div>
      <nav aria-label="Footer" class="foot-nav">
        <a href="#solutions">Solutions</a>
        <a href="#process">Process</a>
        <a href="#insights">Value</a>
        <a href="#articles">Articles</a>
        <a href="/auth/login">Login</a>
      </nav>
      <div class="foot-copy">&copy; <?= date('Y') ?> RSD Portal. Original content & design adapted uniquely.</div>
    </div>
  </footer>

  <script>
    const io = new IntersectionObserver((entries)=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}})},{threshold:.15});
    document.querySelectorAll('[data-fade]').forEach(el=>io.observe(el));
    if(window.matchMedia('(prefers-reduced-motion: reduce)').matches){document.documentElement.classList.add('reduced-motion');}

    // Dual intent toggle
    const intentBtns = document.querySelectorAll('.intent-btn');
    intentBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        intentBtns.forEach(b => {b.classList.remove('active'); b.setAttribute('aria-pressed','false');});
        btn.classList.add('active'); btn.setAttribute('aria-pressed','true');
        const target = btn.dataset.target;
        document.querySelectorAll('.intent-panel').forEach(p => p.classList.remove('show'));
        document.getElementById('panel-' + target).classList.add('show');
      });
    });

    // Testimonials slider
    const slides = Array.from(document.querySelectorAll('.slide'));
    let current = 0;
    const prevBtn = document.querySelector('.slide-nav.prev');
    const nextBtn = document.querySelector('.slide-nav.next');
    const dotsWrap = document.querySelector('.dots');
    function renderDots(){
      dotsWrap.innerHTML='';
      slides.forEach((_,i)=>{
        const d=document.createElement('button');
        if(i===current) d.classList.add('active');
        d.addEventListener('click',()=>{go(i);});
        dotsWrap.appendChild(d);
      });
    }
    function go(index){
      slides[current].classList.remove('current');
      current = (index + slides.length) % slides.length;
      slides[current].classList.add('current');
      renderDots();
    }
    prevBtn && prevBtn.addEventListener('click',()=>go(current-1));
    nextBtn && nextBtn.addEventListener('click',()=>go(current+1));
    renderDots();

    // FAQ accordion
    document.querySelectorAll('.faq-q').forEach(q => {
      q.addEventListener('click', () => {
        const expanded = q.getAttribute('aria-expanded') === 'true';
        q.setAttribute('aria-expanded', String(!expanded));
        const ans = q.nextElementSibling; if(!expanded){ ans.hidden = false; } else { ans.hidden = true; }
      });
    });
  </script>
</body>
</html>