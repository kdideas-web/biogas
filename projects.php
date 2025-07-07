<?php
require_once 'includes/header.php';
require_once 'includes/db.php'; // Site's main DB connection

// Fetch projects from the database
$projects = [];
$sql = "SELECT id, title, description, image_url, DATE_FORMAT(date_completed, '%M %Y') AS date_completed_formatted, location
        FROM projects
        ORDER BY date_completed DESC, created_at DESC"; // Show newest completed first
$result = mysqli_query($link, $sql);
if ($result) {
    $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    // Optionally log error or display a user-friendly message
    // For now, if $projects is empty, the page will show "No projects found."
}
// mysqli_close($link) // Closed in footer.php
?>

<div class="container">
    <section id="projects" class="py-5">
        <h1 class="display-5 text-center mb-5">Our Flagship Projects</h1>
        <p class="lead text-center mb-5">We take pride in the positive impact our biogas solutions have made. Explore some examples of our work below.</p>

        <?php if (empty($projects)): ?>
            <div class="alert alert-info text-center" role="alert">
                No projects found at the moment. Please check back soon for updates on our latest work!
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($projects as $project): ?>
                    <div class="col-md-6 col-lg-4 mb-4 project-card">
                        <div class="card h-100 shadow">
                            <?php
                                $image_path = !empty($project['image_url']) ? htmlspecialchars($project['image_url']) : 'https://via.placeholder.com/400x250.png?text=Project+Image';
                            ?>
                            <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($project['title']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <?php if (!empty($project['location'])): ?>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($project['location']); ?></h6>
                                <?php endif; ?>
                                <p class="card-text small">
                                    <?php
                                        // Truncate description if too long
                                        $description_short = htmlspecialchars($project['description']);
                                        if (strlen($description_short) > 150) {
                                            $description_short = substr($description_short, 0, 147) . '...';
                                        }
                                        echo $description_short;
                                    ?>
                                </p>
                                <ul class="list-unstyled mt-auto mb-2 small">
                                    <?php if (!empty($project['date_completed_formatted'])): ?>
                                        <li><strong>Completed:</strong> <?php echo htmlspecialchars($project['date_completed_formatted']); ?></li>
                                    <?php endif; ?>
                                    <!-- You can add more dynamic fields here if they exist in your DB, like 'capacity' or 'feedstock' -->
                                </ul>
                                <!-- In a full version, this might link to a single project detail page: project_detail.php?id=<?php echo $project['id']; ?> -->
                                <a href="#" class="btn btn-sm btn-outline-info align-self-start disabled">View Case Study (Soon)</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                 <!-- "Your Project Here?" Card - can be kept if desired -->
                <div class="col-md-6 col-lg-4 mb-4 project-card">
                    <div class="card h-100 shadow">
                         <div class="card-body text-center d-flex flex-column justify-content-center align-items-center" style="min-height: 350px;"> <!-- Ensure consistent height -->
                            <h5 class="card-title">Your Project Here?</h5>
                            <p>We are always looking for new partners to help build a sustainable future. If you have a project in mind, let's talk!</p>
                            <a href="contact.php" class="btn btn-success mt-3">Contact Us About Your Project</a>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
