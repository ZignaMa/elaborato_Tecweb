const btn = document.querySelector("body > header > nav ul li:last-child");
const nav = document.querySelector("body > header > nav");
const menu = document.querySelector("body > header > nav div");
const icon_closed = "uploads/static/icons/chevron-right.svg";
const icon_open = "uploads/static/icons/chevron-down.svg";

// small timeout to avoid closing when pointer briefly passes a tiny gap
let hideTimer = null;

function show_popup_menu() {
  if (!menu || !btn || !nav) {
    // nothing to do when not logged in
    return;
  }
  const icon = btn.querySelector("img:last-child");
  if (icon) {
    icon.src = icon.src.replace(icon_closed, icon_open);
  }

  // compute position so the menu sits flush under the button (no gap)
  const btnRect = btn.getBoundingClientRect();
  const navRect = nav.getBoundingClientRect();
  const desiredWidth = Math.max(btnRect.width - 2, 120);
  menu.style.width = desiredWidth + "px";
  // top relative to nav (nav is positioned), align menu top to button bottom
  menu.style.top = (btnRect.bottom - navRect.top) + "px";
  // align right edge of menu with right edge of button
  menu.style.right = (navRect.right - btnRect.right) + "px";

  menu.style.visibility = "visible";

  if (hideTimer) {
    clearTimeout(hideTimer);
    hideTimer = null;
  }
}

function hide_popup_menu_immediate() {
  if (!menu || !btn) return;
  const icon = btn.querySelector("img:last-child");
  if (icon) {
    icon.src = icon.src.replace(icon_open, icon_closed);
  }
  menu.style.visibility = "hidden";
}

function hide_popup_menu() {
  // Delay hide slightly to allow mouse to cross small gaps
  if (hideTimer) clearTimeout(hideTimer);
  hideTimer = setTimeout(() => {
    hide_popup_menu_immediate();
    hideTimer = null;
  }, 150);
}

if (menu != null && btn != null) {
  btn.addEventListener("click", (event) => {
    if (menu.style.visibility != "visible") {
      show_popup_menu();
    } else {
      hide_popup_menu_immediate();
    }
  });
  btn.addEventListener("mouseenter", (event) => {
    show_popup_menu();
  });
  menu.addEventListener("mouseenter", (event) => {
    show_popup_menu();
  });
  btn.addEventListener("mouseleave", (event) => {
    hide_popup_menu();
  });
  menu.addEventListener("mouseleave", (event) => {
    hide_popup_menu();
  });
  window.addEventListener("click", (event) => {
    if (!btn.contains(event.target) && !menu.contains(event.target)) {
      hide_popup_menu_immediate();
    }
  });
}
