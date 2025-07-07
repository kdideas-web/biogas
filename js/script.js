// Custom JavaScript will go here
console.log("Biogas Technologies Accra website script loaded.");

document.addEventListener('DOMContentLoaded', function () {
    // Smooth scrolling for internal links (e.g., from homepage service cards to service page sections)
    // This is a more generic smooth scroll for any #hash links that might exist.
    document.querySelectorAll('a[href*="#"]:not([href="#"]):not([data-bs-toggle="collapse"]):not([data-bs-toggle="tab"]):not([data-bs-toggle="dropdown"]):not(.carousel-control-prev):not(.carousel-control-next)').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            // Check if the link is to a different page or the same page
            if (this.hostname === window.location.hostname && this.pathname === window.location.pathname) {
                // Same page link
                e.preventDefault();
                const targetId = this.hash; // Get the hash directly
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    const headerOffset = 70; // Height of fixed navbar
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Close mobile navbar if open
                    const navbarToggler = document.querySelector('.navbar-toggler');
                    const navbarCollapse = document.querySelector('.navbar-collapse.show');
                    if (navbarToggler && navbarCollapse) {
                        var bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
                        bsCollapse.hide();
                    }
                }
            }
            // If it's a link to a different page with a hash, the browser will handle the initial jump,
            // then we can refine the scroll on the target page if needed (see below).
        });
    });

    // On page load, if there's a hash in the URL, scroll to it smoothly respecting navbar offset
    if (window.location.hash) {
        const targetId = window.location.hash;
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            // Wait for a moment to ensure page layout is complete
            setTimeout(() => {
                const headerOffset = 70; // Height of fixed navbar
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }, 100); // Small delay
        }
    }


    // Clear success/error messages from URL on contact page after a short delay
    if (document.body.classList.contains('contact-php')) { // Assuming a class 'contact-php' is added to body in contact.php
        if (window.location.search.includes('submission=')) {
            setTimeout(() => {
                const url = new URL(window.location);
                url.searchParams.delete('submission');
                window.history.replaceState({}, document.title, url.pathname + url.hash); // Clean URL without reload
            }, 5000); // Clear after 5 seconds
        }
    }
     // Generalizing the above for any page that might use this submission parameter:
     if (window.location.search.includes('submission=')) {
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('submission');
            // Retain hash if it exists
            const currentHash = window.location.hash;
            window.history.replaceState({}, document.title, url.pathname + (currentHash ? currentHash : ''));
        }, 5000);
    }


    // Client-side validation for contact form (and potentially other forms)
    const forms = document.querySelectorAll('.needs-validation, #contactForm'); // Add .needs-validation to forms that need it
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Mobile Navbar Toggler: Ensure it closes when a link is clicked
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbarCollapseElement = document.querySelector('.navbar-collapse');
    if (navbarCollapseElement) { // Check if it exists
        const bsCollapse = new bootstrap.Collapse(navbarCollapseElement, { toggle: false });
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapseElement.classList.contains('show')) {
                    bsCollapse.hide();
                }
            });
        });
    }
});
