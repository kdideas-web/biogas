<?php
$page_title = "Admin Dashboard"; // Set page title
require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?php echo htmlspecialchars($page_title); ?></h1>
</div>

<p>Welcome to the BioGas Accra Admin Panel, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
<p>From here, you can manage various aspects of the website. Use the navigation bar above to access different modules.</p>

<div class="row mt-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Manage Projects</h5>
                <p class="card-text">Add, edit, or delete project showcases.</p>
                <a href="manage_projects.php" class="btn btn-primary">Go to Projects</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Manage Blog Posts</h5>
                <p class="card-text">Create and update blog articles.</p>
                <a href="manage_posts.php" class="btn btn-primary">Go to Blog</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Site Settings</h5>
                <p class="card-text">Update general site information.</p>
                <a href="site_settings.php" class="btn btn-primary">Go to Settings</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
