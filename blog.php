<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Pagination variables
$posts_per_page = 6; // Number of posts to display per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $posts_per_page;

// Fetch total number of published posts for pagination
$total_posts_sql = "SELECT COUNT(*) as total FROM posts WHERE status = 'published' AND published_at <= NOW()";
$total_posts_result = mysqli_query($link, $total_posts_sql);
$total_posts_row = mysqli_fetch_assoc($total_posts_result);
$total_posts = $total_posts_row['total'];
$total_pages = ceil($total_posts / $posts_per_page);


// Fetch published posts for the current page
$posts = [];
$sql = "SELECT p.id, p.title, p.slug, p.content, p.image_url,
               DATE_FORMAT(p.published_at, '%M %e, %Y') AS published_date_formatted,
               u.username AS author_username
        FROM posts p
        LEFT JOIN users u ON p.author_id = u.id
        WHERE p.status = 'published' AND p.published_at <= NOW()
        ORDER BY p.published_at DESC
        LIMIT ? OFFSET ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ii", $posts_per_page, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result) {
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
    }
    mysqli_stmt_close($stmt);
} else {
    // Handle error - e.g., log it or display a friendly message
    // For now, $posts will remain empty and the page will show "No posts found."
}
?>

<div class="container">
    <section id="blog" class="py-5">
        <h1 class="display-5 text-center mb-5">Our Biogas Insights & News</h1>
        <p class="lead text-center mb-5">Stay updated with the latest developments in biogas technology, sustainable energy, and our work at BioGas Accra.</p>

        <?php if (empty($posts)): ?>
            <div class="alert alert-info text-center" role="alert">
                No blog posts found. Please check back later!
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow blog-post-card">
                            <?php
                                $image_path = !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'https://via.placeholder.com/400x250.png?text=Blog+Image';
                            ?>
                            <a href="single_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>">
                                <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="single_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="text-decoration-none text-dark stretched-link-pseudo"> <!-- Stretched link behavior on title -->
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h5>
                                <p class="card-text small text-muted">
                                    By <?php echo htmlspecialchars($post['author_username'] ?? 'Admin'); ?>
                                    on <?php echo htmlspecialchars($post['published_date_formatted']); ?>
                                </p>
                                <p class="card-text">
                                    <?php
                                        $content_snippet = strip_tags($post['content']); // Remove HTML tags for snippet
                                        if (strlen($content_snippet) > 120) {
                                            $content_snippet = substr($content_snippet, 0, 117) . '...';
                                        }
                                        echo htmlspecialchars($content_snippet);
                                    ?>
                                </p>
                                <a href="single_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-sm btn-outline-primary mt-auto align-self-start">Read More &raquo;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <!-- Previous Page Link -->
                    <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Previous</a>
                    </li>

                    <!-- Page Number Links -->
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next Page Link -->
                    <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        <?php endif; ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
