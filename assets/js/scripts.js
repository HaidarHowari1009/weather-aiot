document.addEventListener("DOMContentLoaded", function(event) {
    // Sidebar Toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');

    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
    
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.container-fluid');
    if (mainContent) {
        mainContent.classList.add('fade-in');
    }
});
