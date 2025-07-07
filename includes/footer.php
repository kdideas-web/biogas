<!-- Main content ends here -->
    </main>
    <footer class="footer mt-auto py-4 bg-dark text-white">
        <div class="container text-center">
            <p class="mb-0">&copy; <script>document.write(new Date().getFullYear())</script> <?php echo htmlspecialchars(get_site_setting('site_name_footer', 'BioGas Accra')); ?>. All Rights Reserved.</p>
            <p>
                Follow us on:
                <?php
                    $facebook_url = get_site_setting('social_facebook_url');
                    $twitter_url = get_site_setting('social_twitter_url');
                    $linkedin_url = get_site_setting('social_linkedin_url');
                    $social_links = [];
                    if (!empty($facebook_url)) $social_links[] = '<a href="' . htmlspecialchars($facebook_url) . '" class="text-white" target="_blank" rel="noopener noreferrer">Facebook</a>';
                    if (!empty($twitter_url)) $social_links[] = '<a href="' . htmlspecialchars($twitter_url) . '" class="text-white" target="_blank" rel="noopener noreferrer">Twitter</a>';
                    if (!empty($linkedin_url)) $social_links[] = '<a href="' . htmlspecialchars($linkedin_url) . '" class="text-white" target="_blank" rel="noopener noreferrer">LinkedIn</a>';

                    echo implode(' | ', $social_links);
                    if (empty($social_links)) echo 'Social media links coming soon.';
                ?>
            </p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html>
