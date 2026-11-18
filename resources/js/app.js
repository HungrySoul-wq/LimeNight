/* =====================================================
   LIMENIGHT — ОСНОВНОЙ JAVASCRIPT
===================================================== */

/* =====================================================
   МОБИЛЬНОЕ МЕНЮ
   Открывает и закрывает навигацию на телефонах
===================================================== */

document.addEventListener('DOMContentLoaded', () => {
    const menuButton = document.querySelector('#mobile-menu-button');
    const mobileMenu = document.querySelector('#mobile-menu');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
