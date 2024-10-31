window.onload = function() {
    // Try to get both possible content div IDs
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content') || document.getElementById('main-content'); // Support both IDs
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Check if the required elements exist
    if (sidebar && content && sidebarToggle) {
        let isSidebarCollapsed = sidebar.classList.contains('collapsed');

        // Add event listener to the sidebar toggle button
        sidebarToggle.addEventListener('click', function(event) {
            event.stopPropagation();  // Prevent event bubbling

            if (isSidebarCollapsed) {
                // Open sidebar
                sidebar.classList.remove('collapsed');
                content.classList.remove('collapsed');
                sidebarToggle.style.color = 'white';
            } else {
                // Close sidebar
                sidebar.classList.add('collapsed');
                content.classList.add('collapsed');
                sidebarToggle.style.color = 'black';
            }

            // Toggle the state
            isSidebarCollapsed = !isSidebarCollapsed;
        });
    } else {
        console.error("Required elements not found (#sidebar, #content or #main-content, #sidebarToggle). Check HTML structure.");
    }
};