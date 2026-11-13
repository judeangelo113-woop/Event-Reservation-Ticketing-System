const menuToggle = document.getElementById('menuToggle');
const appSidebar = document.getElementById('appSidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (menuToggle && appSidebar && sidebarOverlay) {

    function toggleSidebar() {
        appSidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');

        const isOpen = appSidebar.classList.contains('active');

        menuToggle.setAttribute('aria-expanded', isOpen);
    }

    menuToggle.addEventListener('click', toggleSidebar);

    sidebarOverlay.addEventListener('click', toggleSidebar);

}
