
const mobileMenuBtn = document.getElementById("mobile-menu-btn");
const mobileNav = document.getElementById("mobile-nav");

if (mobileMenuBtn && mobileNav) {
  mobileMenuBtn.addEventListener("click", () => {
    mobileNav.classList.toggle("open");
    const icon = mobileMenuBtn.querySelector("i");
    if (icon) {
      icon.classList.toggle("bi-list");
      icon.classList.toggle("bi-x");
    }
  });
  document.addEventListener("click", (e) => {
    if (!mobileMenuBtn.contains(e.target) && !mobileNav.contains(e.target)) {
      mobileNav.classList.remove("open");
    }
  });
}


