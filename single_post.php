<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$post_slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$post_id_from_url = isset($_GET['id']) ? intval($_GET['id']) : 0; // Fallback if slug isn't found or used

$post = null;

if (!empty($post_slug)) {
    $sql = "SELECT p.id, p.title, p.slug, p.content, p.image_url,
                   DATE_FORMAT(p.published_at, '%M %e, %Y at %h:%i %p') AS published_date_formatted,
                   p.created_at, p.updated_at,
                   u.username AS author_username
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.slug = ? AND p.status = 'published' AND p.published_at <= NOW()";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $post_slug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $post = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
} elseif ($post_id_from_url > 0) { // Fallback to ID if slug is not provided or not found
     $sql = "SELECT p.id, p.title, p.slug, p.content, p.image_url,
                   DATE_FORMAT(p.published_at, '%M %e, %Y at %h:%i %p') AS published_date_formatted,
                   p.created_at, p.updated_at,
                   u.username AS author_username
            FROM posts p
            LEFT JOIN users u ON p.author_id = u.id
            WHERE p.id = ? AND p.status = 'published' AND p.published_at <= NOW()";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $post_id_from_url);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            $post = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }
}


// Update page title dynamically
if ($post) {
    $page_title = htmlspecialchars($post['title']); // Set this before header.php is included (oops, header already included)
                                                // This means dynamic title in header for this page won't work as is.
                                                // A workaround would be to buffer header output or set title via JS.
                                                // For now, we'll just output title in the body.
} else {
    $page_title = "Post Not Found";
}

?>

<div class="container mt-4 mb-5">
    <?php if ($post): ?>
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <article class="blog-post">
                    <h1 class="blog-post-title display-5 mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p class="blog-post-meta text-muted">
                        Published on <?php echo htmlspecialchars($post['published_date_formatted']); ?>
                        by <a href="#"><?php echo htmlspecialchars($post['author_username'] ?? 'Admin'); ?></a>
                    </p>

                    <?php if (!empty($post['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($post['title']); ?>" style="max-height: 450px; width: 100%; object-fit: cover;">
                    <?php endif; ?>

                    <div class="blog-post-content">
                        <?php
                            // Basic HTML is allowed in content. For security with user-generated HTML,
                            // use a proper HTML purifier library in a real application.
                            echo $post['content'];
                        ?>
                    </div>
                </article>

                <hr class="my-5">

                <!-- Basic Social Share (conceptual, replace # with actual share URLs) -->
                <div class="social-share mb-4">
                    <h5 class="mb-2">Share this post:</h5>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>" target="_blank" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook-f"></i> Facebook</a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-outline-info btn-sm me-2"><i class="fab fa-twitter"></i> Twitter</a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode( "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>&title=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                    <a href="whatsapp://send?text=<?php echo urlencode($post['title'] . " " . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ); ?>" data-action="share/whatsapp/share" class="btn btn-outline-success btn-sm"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                </div>
                 <!-- Font Awesome for social icons (ensure it's linked, e.g. in main footer or here) -->
                 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


                <div class="text-center">
                     <a href="blog.php" class="btn btn-secondary">&laquo; Back to Blog</a>
                </div>

            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            <h2 class="alert-heading">Post Not Found</h2>
            <p>Sorry, the blog post you are looking for could not be found or may not be published yet.</p>
            <hr>
            <a href="blog.php" class="btn btn-primary">&laquo; Back to Blog</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Workaround for dynamic title since header is already included.
// This is not ideal but works for this context.
if ($post) {
    echo "<script>document.title = \"" . htmlspecialchars($post['title'], ENT_QUOTES) . " - BioGas Accra\";</script>";
} else {
     echo "<script>document.title = \"Post Not Found - BioGas Accra\";</script>";
}
?>
<?php require_once 'includes/footer.php'; ?>
