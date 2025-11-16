// ================= HEADER INTERACTIONS =================
document.addEventListener("DOMContentLoaded", () => {
  const header = document.getElementById("siteHeader");
  const menuBtn = document.getElementById("menuBtn");
  const mobileMenu = document.getElementById("mobileMenu");

  let lastScroll = 0;

  // Scroll direction logic
  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset;

    // Add background after 60px
    if (currentScroll > 60) {
      header.classList.add("active");
    } else {
      header.classList.remove("active");
    }

    // Hide on scroll down, show on scroll up
    if (currentScroll > lastScroll && currentScroll > 200) {
      header.classList.add("hide");
    } else {
      header.classList.remove("hide");
    }

    lastScroll = currentScroll;
  });

  // Toggle mobile menu
  menuBtn.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
    menuBtn.classList.toggle("text-accent");
  });

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (!header.contains(e.target) && mobileMenu.classList.contains("show")) {
      mobileMenu.classList.remove("show");
      menuBtn.classList.remove("text-accent");
    }
  });
});
