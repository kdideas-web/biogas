<?php
$page_title = "Site Settings";
require_once 'includes/header.php';
require_once '../includes/db.php';

// Define the settings we want to manage
// Keys should match what you'll use in get_site_setting() and in the form
$defined_settings = [
    'home_core_services_title' => ['label' => 'Homepage "Our Core Services" Title', 'type' => 'text', 'default' => 'Our Core Services'],
    'home_welcome_title'       => ['label' => 'Homepage "Welcome" Title', 'type' => 'text', 'default' => 'Welcome to BioGas Accra'],
    'home_welcome_paragraph'   => ['label' => 'Homepage "Welcome" Paragraph', 'type' => 'textarea', 'default' => 'We are a leading provider of innovative biogas solutions...'],
    'contact_phone'            => ['label' => 'Site Contact Phone Number', 'type' => 'text', 'default' => '+233 24 123 4567'],
    'contact_email'            => ['label' => 'Site Contact Email Address', 'type' => 'email', 'default' => 'info@biogasaccra.com'],
    'contact_address'          => ['label' => 'Site Physical Address', 'type' => 'textarea', 'default' => "123 Biogas Lane, East Legon\nAccra, Ghana"],
    'social_facebook_url'      => ['label' => 'Facebook Profile URL', 'type' => 'url', 'default' => 'https://facebook.com/yourpage'],
    'social_twitter_url'       => ['label' => 'Twitter Profile URL', 'type' => 'url', 'default' => 'https://twitter.com/yourprofile'],
    'social_linkedin_url'      => ['label' => 'LinkedIn Profile URL', 'type' => 'url', 'default' => 'https://linkedin.com/in/yourprofile'],
    'site_name_footer'         => ['label' => 'Site Name (for Footer Copyright)', 'type' => 'text', 'default' => 'BioGas Accra'],
    // Add more settings here as needed
];

// Fetch current settings from DB
$current_settings = [];
$sql_fetch = "SELECT setting_name, setting_value FROM site_settings";
$result_fetch = mysqli_query($link, $sql_fetch);
if ($result_fetch) {
    while ($row = mysqli_fetch_assoc($result_fetch)) {
        $current_settings[$row['setting_name']] = $row['setting_value'];
    }
    mysqli_free_result($result_fetch);
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Error fetching site settings: ' . mysqli_error($link)];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $all_updates_successful = true;
    mysqli_begin_transaction($link); // Start transaction

    foreach ($defined_settings as $name => $details) {
        if (isset($_POST[$name])) {
            $value = trim($_POST[$name]);

            // Use INSERT ... ON DUPLICATE KEY UPDATE to insert or update settings
            $sql_upsert = "INSERT INTO site_settings (setting_name, setting_value) VALUES (?, ?)
                           ON DUPLICATE KEY UPDATE setting_value = ?";
            if ($stmt_upsert = mysqli_prepare($link, $sql_upsert)) {
                mysqli_stmt_bind_param($stmt_upsert, "sss", $name, $value, $value);
                if (!mysqli_stmt_execute($stmt_upsert)) {
                    $all_updates_successful = false;
                    // Log specific error: error_log("Error updating setting $name: " . mysqli_stmt_error($stmt_upsert));
                }
                mysqli_stmt_close($stmt_upsert);
            } else {
                $all_updates_successful = false;
                 // Log specific error: error_log("Error preparing statement for setting $name: " . mysqli_error($link));
            }
        }
        if (!$all_updates_successful) break; // Exit loop on first error
    }

    if ($all_updates_successful) {
        mysqli_commit($link);
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Site settings updated successfully!'];
    } else {
        mysqli_rollback($link);
        $_SESSION['flash_message'] = ['type' => 'error', 'text' => 'Error updating site settings. Some settings might not have been saved. Check server logs for details.'];
    }
    // Refresh current settings after update
    header("Location: site_settings.php"); // Redirect to show updated values and flash message
    exit;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
</div>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <?php foreach ($defined_settings as $name => $details): ?>
        <?php $value = isset($current_settings[$name]) ? $current_settings[$name] : $details['default']; ?>
        <div class="mb-3">
            <label for="<?php echo htmlspecialchars($name); ?>" class="form-label"><?php echo htmlspecialchars($details['label']); ?></label>
            <?php if ($details['type'] == 'textarea'): ?>
                <textarea class="form-control" id="<?php echo htmlspecialchars($name); ?>" name="<?php echo htmlspecialchars($name); ?>" rows="3"><?php echo htmlspecialchars($value); ?></textarea>
            <?php else: ?>
                <input type="<?php echo htmlspecialchars($details['type']); ?>" class="form-control" id="<?php echo htmlspecialchars($name); ?>" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo htmlspecialchars($value); ?>">
            <?php endif; ?>
            <?php if (isset($details['help_text'])): ?>
                <small class="form-text text-muted"><?php echo htmlspecialchars($details['help_text']); ?></small>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>

<?php require_once 'includes/footer.php'; ?>
