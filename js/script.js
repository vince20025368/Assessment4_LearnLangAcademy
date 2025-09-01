(function () {
  const btn = document.querySelector(".menu-toggle");
  const menu = document.getElementById("main-menu");

  if (!btn || !menu) return;

  function closeMenu() {
    menu.classList.remove("active");
    btn.setAttribute("aria-expanded", "false");
  }

  btn.addEventListener("click", (e) => {
    const isOpen = menu.classList.toggle("active");
    btn.setAttribute("aria-expanded", isOpen ? "true" : "false");
    e.stopPropagation();
  });

  // Close on outside click
  document.addEventListener("click", (e) => {
    if (!menu.classList.contains("active")) return;
    if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
  });

  // Close on ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeMenu();
  });

  // Close after choosing a link
  menu.addEventListener("click", (e) => {
    if (e.target.closest("a")) closeMenu();
  });
})();
