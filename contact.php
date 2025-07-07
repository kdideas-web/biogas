<?php require_once 'includes/header.php'; ?>
<?php
// The session_start() is already in header.php
// Logic to handle form feedback messages and data repopulation
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$form_errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : [];
// Clear session variables after retrieving them to prevent them from persisting across page loads
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Pre-fill subject if 'service' GET parameter is present
$subject_preset = '';
if (isset($_GET['service'])) {
    switch ($_GET['service']) {
        case 'domestic':
            $subject_preset = 'Inquiry about Domestic Biogas Systems';
            break;
        case 'commercial':
            $subject_preset = 'Inquiry about Commercial/Industrial Biogas Solutions';
            break;
        case 'agricultural':
            $subject_preset = 'Inquiry about Agricultural Biogas Plants';
            break;
        case 'consultancy':
            $subject_preset = 'Inquiry about Consultancy & Support';
            break;
        default:
            $subject_preset = 'General Inquiry';
    }
}

?>
<div class="container">
    <!-- Contact Us Section Content -->
    <section id="contact" class="py-5">
        <h1 class="display-5 text-center mb-5">Get In Touch With Us</h1>
        <p class="lead text-center mb-5">We're here to answer your questions and help you find the perfect biogas solution. Reach out to us via the form below, or contact us directly through phone or email.</p>

        <!-- Server Message Placeholders -->
        <div id="contactFormMessages" class="mb-4">
            <?php
            if (isset($_GET['submission'])) {
                if ($_GET['submission'] == 'success' && isset($_SESSION['success_message'])) {
                    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
                    unset($_SESSION['success_message']); // Clear the message after displaying
                } elseif (isset($_SESSION['error_message'])) { // Covers 'error', 'dberror', 'validation_error' if generic error message is set
                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                    unset($_SESSION['error_message']); // Clear the message
                }
                // Specific handling for validation_error if a general error message wasn't set but specific errors exist
                if ($_GET['submission'] == 'validation_error' && !empty($form_errors) && !isset($_SESSION['error_message'])) {
                     echo '<div class="alert alert-danger" role="alert">Please correct the errors highlighted below.</div>';
                }
            }
            ?>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h3 class="mb-4">Send Us a Message</h3>
                <form action="contact_process.php" method="POST" id="contactForm" novalidate>
                    <div class="mb-3">
                        <label for="contactName" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($form_errors['name']) ? 'is-invalid' : ''; ?>" id="contactName" name="name" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required>
                        <?php if (isset($form_errors['name'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($form_errors['name']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="contactEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control <?php echo isset($form_errors['email']) ? 'is-invalid' : ''; ?>" id="contactEmail" name="email" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                        <?php if (isset($form_errors['email'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($form_errors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="contactSubject" class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php echo isset($form_errors['subject']) ? 'is-invalid' : ''; ?>" id="contactSubject" name="subject" value="<?php echo htmlspecialchars($form_data['subject'] ?? $subject_preset); ?>" required>
                        <?php if (isset($form_errors['subject'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($form_errors['subject']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php echo isset($form_errors['message']) ? 'is-invalid' : ''; ?>" id="contactMessage" name="message" rows="5" required><?php echo htmlspecialchars($form_data['message'] ?? ''); ?></textarea>
                        <?php if (isset($form_errors['message'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($form_errors['message']); ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                </form>
            </div>
            <div class="col-lg-5">
                <h3 class="mb-4">Contact Information</h3>
                <p><strong>BioGas Accra Ltd.</strong></p>
                <p>
                    <i class="fas fa-map-marker-alt me-2"></i> 123 Biogas Lane, East Legon<br>
                    Accra, Greater Accra Region, Ghana
                </p>
                <p>
                    <i class="fas fa-phone me-2"></i> <a href="tel:+233241234567">+233 24 123 4567</a>
                </p>
                <p>
                    <i class="fas fa-envelope me-2"></i> <a href="mailto:info@biogasaccra.com">info@biogasaccra.com</a>
                </p>
                <p>
                    <i class="fas fa-clock me-2"></i> <strong>Business Hours:</strong><br>
                    Monday - Friday: 8:00 AM - 5:00 PM<br>
                    Saturday: 9:00 AM - 1:00 PM (By Appointment)<br>
                    Sunday: Closed
                </p>
                <h4 class="mt-4 mb-3">Find Us</h4>
                <!-- Placeholder for Google Maps Embed -->
                <div class="map-responsive">
                     <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127057.15486173925!2d-0.27979039093077586!3d5.625011554000289!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9b70e6e08a87%3A0x42023769f17ebd7a!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1706100000000!5m2!1sen!2sus" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Font Awesome for icons (if not already included elsewhere, or for specific icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php require_once 'includes/footer.php'; ?>
