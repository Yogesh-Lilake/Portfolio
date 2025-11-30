// ===== Reveal Animations =====
    const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    const revealObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) entry.target.classList.add('active');
        else entry.target.classList.remove('active');
        // Keeps scroll smooth on low-end mobile GPUs.
      // else entry.target.classList.remove('active'); // adds re-animate on scroll up
      });
    }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
    reveals.forEach(el => revealObserver.observe(el));

    // ===== Parallax (desktop only) =====
    if (window.innerWidth > 768) {
      window.addEventListener('scroll', () => {
        const y = window.scrollY * 0.3;
        document.querySelector('.parallax-bg').style.backgroundPosition = `center ${y}px`;
      });
    }