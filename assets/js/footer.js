  // Optional: subtle parallax motion for orbs on scroll
  document.addEventListener('scroll', () => {
    const scrollY = window.scrollY * 0.2;
    document.querySelectorAll('.orb').forEach((orb, i) => {
      orb.style.transform = `translateY(${scrollY * (i + 1) * 0.2}px)`;
    });
  });
