<?php
  session_start(); // Start session for messages, etc.
  require_once __DIR__ . '/db.php'; // Ensure DB connection is available for settings helper
  require_once __DIR__ . '/settings_helper.php'; // Include the settings helper

  // Define the current page based on the script name
  $current_page = basename($_SERVER['PHP_SELF']);
  $site_name = get_site_setting('site_name', 'BioGas Accra'); // Example for site name
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dynamic page title -->
    <title><?php
        switch ($current_page) {
            case 'index.php':
            case 'home.php':
                echo 'Home';
                break;
            case 'about.php':
                echo 'About Us';
                break;
            case 'services.php':
                echo 'Our Services';
                break;
            case 'projects.php':
                echo 'Our Projects';
                break;
            case 'benefits.php':
                echo 'Benefits of Biogas';
                break;
            case 'blog.php':
                echo 'Blog';
                break;
            case 'contact.php':
                echo 'Contact Us';
                break;
            default:
                echo 'BioGas Accra';
        }
    ?> - BioGas Accra</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?php echo $current_page == 'home.php' || $current_page == 'index.php' ? '' : 'pt-5'; // Add padding top for non-home pages due to fixed nav ?>">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <div class="container">
                <a class="navbar-brand" href="index.php">BioGas Accra</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php' || $current_page == 'home.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'about.php') ? 'active' : ''; ?>" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>" href="services.php">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>" href="projects.php">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'benefits.php') ? 'active' : ''; ?>" href="benefits.php">Benefits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>" href="blog.php">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="<?php echo $current_page == 'home.php' || $current_page == 'index.php' ? '' : 'mt-5 py-5'; // Add margin top for non-home pages ?>">
    <!-- Main content starts here -->
