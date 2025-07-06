// Custom JavaScript will go here
console.log("Biogas Technologies Accra website script loaded.");

document.addEventListener('DOMContentLoaded', function () {
    // Activate Bootstrap scrollspy on the main nav element
    const mainNav = document.body.querySelector('#navbarNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#navbarNav',
            offset: 72, // Height of fixed navbar + some offset (increased slightly)
        });
    }

    // Smooth scrolling for internal links in the navbar
    document.querySelectorAll('#navbarNav a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const headerOffset = 70; // Must match scrollspy offset and CSS padding-top for sections
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                const navbarToggler = document.querySelector('.navbar-toggler');
                const navbarCollapse = document.querySelector('.navbar-collapse.show');
                if (navbarToggler && navbarCollapse) {
                    // Bootstrap 5 toggler might not need a click, but directly hide the collapse element
                     var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                         toggle: false // an instance is Created, but not toggled
                     });
                     bsCollapse.hide();
                }
            }
        });
    });

    // Clear success/error messages from URL after a short delay or on next interaction
    // to prevent them from showing up if the page is refreshed manually.
    if (window.location.search.includes('submission=')) {
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('submission');
            // Also remove specific error/success session keys if they were for form to avoid reshowing
            // This part is more reliably handled server-side by unsetting session variables.
            // Client-side can just clean the URL.
            window.history.replaceState({}, document.title, url.pathname + url.hash); // Clean URL without reload
        }, 5000); // Clear after 5 seconds
    }

    // Client-side validation example (optional, as server-side is primary)
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            // Basic check, Bootstrap's `required` attribute handles most simple cases
            if (!contactForm.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                // Optionally add more custom validation feedback here
            }
            contactForm.classList.add('was-validated');
        }, false);
    }
});
