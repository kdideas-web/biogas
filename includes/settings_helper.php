<?php
// Function to get a site setting value
// Caches settings in a static variable to reduce DB queries per page load

function get_site_setting($setting_name, $default_value = '') {
    static $settings_cache = null; // Static variable to cache settings

    // If cache is null, it's the first call, so populate the cache
    if ($settings_cache === null) {
        global $link; // Assuming $link is your global mysqli connection from db.php
        $settings_cache = []; // Initialize as an array

        if (!$link) {
            // This case should ideally not happen if db.php is included correctly before calling this.
            // error_log("Database connection is not available in get_site_setting.");
            // To prevent errors on pages if DB is down, you might return default or handle error.
            // For now, we'll let it proceed, and it will likely fail if $link isn't there.
        }

        $sql = "SELECT setting_name, setting_value FROM site_settings";
        $result = mysqli_query($link, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $settings_cache[$row['setting_name']] = $row['setting_value'];
            }
            mysqli_free_result($result);
        } else {
            // Log error if settings can't be fetched, but don't break the page
            // error_log("Error fetching site settings for cache: " . mysqli_error($link));
        }
    }

    // Return the setting value from cache if it exists, otherwise return the default value
    if (isset($settings_cache[$setting_name])) {
        return $settings_cache[$setting_name];
    } else {
        return $default_value;
    }
}

// Example of how to ensure $link is available if not already global
// This is usually handled by including db.php before calling functions that need $link.
/*
if (!isset($link) || !$link) {
    // Attempt to include db.php if $link is not set.
    // This path might need adjustment based on where settings_helper.php is included from.
    $db_path = __DIR__ . '/db.php'; // Assumes db.php is in the same directory as this helper
    if (file_exists($db_path)) {
        require_once $db_path;
    } else {
        // Fallback if db.php isn't found, to prevent fatal errors on require_once
        error_log("db.php not found for settings_helper.php. Site settings may not load.");
    }
}
*/
?>
