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

// ===== Lottie Safe Fallback (HARD FAIL SAFE) =====
document.addEventListener('DOMContentLoaded', () => {
  const lotties = document.querySelectorAll('lottie-player[data-fallback]');

  lotties.forEach(original => {
    const fallback = original.dataset.fallback;
    let loaded = false;

    original.addEventListener('load', () => {
      loaded = true;
    });

    setTimeout(() => {
      if (loaded) return;

      console.warn('Lottie failed permanently. Recreating with fallback.');

      // Clone attributes
      const replacement = document.createElement('lottie-player');
      [...original.attributes].forEach(attr => {
        if (attr.name !== 'src') {
          replacement.setAttribute(attr.name, attr.value);
        }
      });

      replacement.setAttribute('src', fallback);

      // Replace poisoned element
      original.replaceWith(replacement);

    }, 1000); // 1–1.5s is ideal
  });
});

