document.addEventListener('DOMContentLoaded', function () {
    
    // Toggle Sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const wrapper = document.getElementById('wrapper');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (e) {
            e.preventDefault();
            wrapper.classList.toggle('toggled');
        });
    }

    // Handle Window Resize for Sidebar
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            wrapper.classList.remove('toggled');
        }
    });

    // Section Switching Logic
    const navLinks = document.querySelectorAll('.nav-link-custom');
    const contentSections = document.querySelectorAll('.content-section');
    const pageTitle = document.getElementById('page-title');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links
            navLinks.forEach(item => item.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Update Page Title
            const targetName = this.textContent.trim();
            pageTitle.textContent = targetName;

            // Hide all sections
            contentSections.forEach(section => {
                section.classList.add('d-none');
                section.classList.remove('active-section');
            });
            
            // Show target section
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            if(targetSection) {
                targetSection.classList.remove('d-none');
                // Small delay to allow display:block to apply before animating opacity if we were using it
                setTimeout(() => {
                    targetSection.classList.add('active-section');
                }, 50);
            }

            // Close sidebar on mobile after clicking a link
            if (window.innerWidth < 768) {
                wrapper.classList.remove('toggled');
            }
        });
    });
});
