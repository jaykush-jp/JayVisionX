/* ===================================================
   JayVisionX - Frontend JavaScript
   =================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ===== AOS INIT ===== */
  AOS.init({
    duration: 700,
    easing: 'ease-out-cubic',
    once: true,
    offset: 60,
  });

  /* ===== STICKY HEADER ===== */
  const header = document.getElementById('site-header');
  function handleScroll() {
    if (window.scrollY > 80) {
      header && header.classList.add('scrolled');
    } else {
      header && header.classList.remove('scrolled');
    }
    // Back to top
    const btn = document.getElementById('backToTop');
    if (btn) {
      btn.classList.toggle('visible', window.scrollY > 400);
    }
  }
  window.addEventListener('scroll', handleScroll, { passive: true });

  /* ===== BACK TO TOP ===== */
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    backToTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ===== MOBILE HAMBURGER MENU ===== */
  const hamburger   = document.getElementById('hamburger');
  const navbarNav   = document.querySelector('.navbar-nav');
  const overlay     = document.getElementById('mobileOverlay');

  function openMenu() {
    navbarNav && navbarNav.classList.add('open');
    overlay   && overlay.classList.add('open');
    hamburger && hamburger.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function closeMenu() {
    navbarNav && navbarNav.classList.remove('open');
    overlay   && overlay.classList.remove('open');
    hamburger && hamburger.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (hamburger) {
    hamburger.addEventListener('click', function () {
      navbarNav && navbarNav.classList.contains('open') ? closeMenu() : openMenu();
    });
  }
  if (overlay) overlay.addEventListener('click', closeMenu);

  // Hamburger animation
  if (hamburger) {
    hamburger.addEventListener('click', function () {
      this.classList.toggle('active');
    });
  }

  /* ===== MOBILE DROPDOWN TOGGLE ===== */
  if (window.innerWidth <= 992) {
    document.querySelectorAll('.nav-item.has-dropdown > .nav-link').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        const parent = this.closest('.nav-item');
        parent.classList.toggle('dropdown-open');
      });
    });
  }

  /* ===== FAQ ACCORDION ===== */
  document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const item   = this.closest('.faq-item');
      const isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.faq-item.open').forEach(function (openItem) {
        openItem.classList.remove('open');
      });

      // Open clicked (if it was closed)
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });

  /* ===== COUNTER ANIMATION ===== */
  function animateCounter(el, target, duration) {
    let start     = 0;
    const step    = Math.ceil(target / (duration / 16));
    const timer   = setInterval(function () {
      start += step;
      if (start >= target) {
        start = target;
        clearInterval(timer);
      }
      el.textContent = start.toLocaleString();
    }, 16);
  }

  // Intersection Observer for counters
  const counterObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        const el     = entry.target;
        const target = parseInt(el.getAttribute('data-count'), 10);
        if (!isNaN(target)) {
          animateCounter(el, target, 1800);
        }
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('.stat-number, .count-num').forEach(function (el) {
    counterObserver.observe(el);
  });

  /* ===== ALERT AUTO DISMISS ===== */
  document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(function () {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity    = '0';
      setTimeout(function () { alert.remove(); }, 500);
    }, 5000);
  });

  /* ===== NEWSLETTER FORM ===== */
  const newsletterForm = document.querySelector('.newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (e) {
      const btn   = this.querySelector('button[type="submit"]');
      const input = this.querySelector('input[type="email"]');
      if (btn && input && input.value) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled  = true;
      }
    });
  }

  /* ===== CONTACT FORM SUBMIT LOADER ===== */
  const contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', function () {
      const btn = this.querySelector('button[type="submit"]');
      if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled  = true;
      }
    });
  }

  /* ===== SMOOTH SCROLL FOR ANCHOR LINKS ===== */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        const offset     = 80; // header height
        const targetTop  = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: targetTop, behavior: 'smooth' });
      }
    });
  });

  /* ===== LAZY LOAD IMAGES (fallback) ===== */
  if ('loading' in HTMLImageElement.prototype) {
    // Browser supports lazy loading natively
  } else {
    // Polyfill for older browsers
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    const imageObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
          }
          imageObserver.unobserve(img);
        }
      });
    });
    lazyImages.forEach(function (img) { imageObserver.observe(img); });
  }

  /* ===== NAVBAR CLOSE ON LINK CLICK (mobile) ===== */
  document.querySelectorAll('.nav-link:not(.has-dropdown .nav-link)').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 992) {
        closeMenu();
      }
    });
  });

  /* ===== HEADER: Close dropdown on outside click ===== */
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.has-dropdown')) {
      document.querySelectorAll('.has-dropdown').forEach(function (item) {
        item.classList.remove('dropdown-open');
      });
    }
  });

  /* ===== EXPERTISE BARS ANIMATION ===== */
  const barObserver = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        const bars = entry.target.querySelectorAll('.expertise-bar-fill');
        bars.forEach(function (bar) {
          const width  = bar.style.width;
          bar.style.width = '0';
          setTimeout(function () {
            bar.style.transition = 'width 1.2s ease';
            bar.style.width      = width;
          }, 100);
        });
        barObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  const expertiseSection = document.querySelector('.expertise-grid');
  if (expertiseSection) barObserver.observe(expertiseSection);

});
