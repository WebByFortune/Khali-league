// JavaScript for responsive navbar functionality
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle'); // Selects the button with ID 'navToggle'
    const mainNavbar = document.getElementById('mainNavbar'); // Selects the nav with ID 'mainNavbar'
    const body = document.body; // Selects the body element for potential layout changes

    navToggle.addEventListener('click', function() {
        mainNavbar.classList.toggle('active'); // Toggles the 'active' class on the navigation menu
        body.classList.toggle('nav-open'); // Optional: Add/remove 'nav-open' class to body for page layout adjustments
        
        // Change the toggle icon between hamburger and 'x'
        const toggleIcon = navToggle.querySelector('i'); // Find the <i> element inside the button
        if (mainNavbar.classList.contains('active')) {
            toggleIcon.classList.remove('fa-bars');
            toggleIcon.classList.add('fa-times'); // Show 'x' icon when open
        } else {
            toggleIcon.classList.remove('fa-times');
            toggleIcon.classList.add('fa-bars'); // Show hamburger icon when closed
        }
    });

    // Optional: Close the navbar when a navigation link is clicked (good for mobile UX)
    mainNavbar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mainNavbar.classList.remove('active');
            body.classList.remove('nav-open');
            // Reset icon after clicking a link
            const toggleIcon = navToggle.querySelector('i');
            if (toggleIcon) { // Check if icon exists before trying to modify
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
            }
        });
    });
});