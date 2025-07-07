<?php require_once 'check_auth.php'; // Ensure user is authenticated ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> - BioGas Accra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin_style.css" rel="stylesheet"> <!-- Custom admin styles -->
    <!-- Add Font Awesome for icons if needed -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> -->
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">BioGas Accra Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_projects.php') ? 'active' : ''; ?>" href="manage_projects.php">Projects</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link <?php
                            $post_pages = ['manage_posts.php', 'add_post.php', 'edit_post.php'];
                            if (in_array(basename($_SERVER['PHP_SELF']), $post_pages)) { echo 'active'; }
                        ?>" href="manage_posts.php">Blog Posts</a>
                    </li>
                    <!-- Add more navigation links here as modules are developed -->
                    <!-- Example:
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_services.php') ? 'active' : ''; ?>" href="manage_services.php">Services</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'site_settings.php') ? 'active' : ''; ?>" href="site_settings.php">Site Settings</a>
                    </li>
                    -->
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text">
                            Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" href="../index.php" target="_blank">View Site</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid" style="margin-top: 70px;"> <!-- Main content container -->
        <div class="row">
            <!-- Sidebar placeholder if needed in future -->
            <!-- <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="manage_projects.php">Projects</a></li>
                    </ul>
                </div>
            </nav> -->
            <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4"> <!-- Adjust col classes if sidebar is added -->
                <!-- Page content will be loaded here -->
                <?php
                // Display session-based flash messages
                if (isset($_SESSION['flash_message'])) {
                    $message = $_SESSION['flash_message'];
                    unset($_SESSION['flash_message']); // Clear message after displaying
                    $alert_type = isset($message['type']) && $message['type'] == 'error' ? 'danger' : 'success';
                    echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show mt-3" role="alert">';
                    echo htmlspecialchars($message['text']);
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                }
                ?>
