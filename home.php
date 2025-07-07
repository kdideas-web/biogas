<?php require_once 'includes/header.php'; ?>

<!-- Bootstrap Carousel -->
<section id="home-hero-carousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#home-hero-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#home-hero-carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#home-hero-carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#home-hero-carousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#home-hero-carousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('https://via.placeholder.com/1920x1080/FF9800/FFFFFF?text=Sustainable+Energy+Future');">
            <div class="carousel-caption d-none d-md-block">
                <h5>Powering Ghana's Sustainable Energy Future</h5>
                <p>Leading the way in innovative biogas solutions for a cleaner, greener tomorrow.</p>
                <a href="services.php" class="btn btn-primary btn-lg">Discover Our Solutions</a>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1920x1080/4CAF50/FFFFFF?text=Waste+to+Wealth');">
            <div class="carousel-caption d-none d-md-block">
                <h5>Transforming Waste into Valuable Resources</h5>
                <p>Efficiently converting organic waste into clean energy and rich bio-fertilizer.</p>
                <a href="benefits.php" class="btn btn-primary btn-lg">See the Benefits</a>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1920x1080/2196F3/FFFFFF?text=Domestic+Biogas+Systems');">
            <div class="carousel-caption d-none d-md-block">
                <h5>Reliable Biogas for Your Home</h5>
                <p>Providing affordable and clean cooking gas for households across Accra.</p>
                 <a href="services.php#domestic" class="btn btn-primary btn-lg">Home Solutions</a>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1920x1080/9C27B0/FFFFFF?text=Agricultural+Innovation');">
            <div class="carousel-caption d-none d-md-block">
                <h5>Empowering Agriculture with Biogas</h5>
                <p>Helping farms reduce costs and improve sustainability with on-site energy production.</p>
                 <a href="services.php#agricultural" class="btn btn-primary btn-lg">Farm Solutions</a>
            </div>
        </div>
        <div class="carousel-item" style="background-image: url('https://via.placeholder.com/1920x1080/F44336/FFFFFF?text=Join+the+Green+Revolution');">
            <div class="carousel-caption d-none d-md-block">
                <h5>Partner with Us for a Greener Ghana</h5>
                <p>Let's work together to build a sustainable future. Contact us for a consultation.</p>
                <a href="contact.php" class="btn btn-success btn-lg">Get a Quote</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#home-hero-carousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#home-hero-carousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</section>

<!-- Abridged About Us Section for Homepage -->
<section id="home-about" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>Welcome to BioGas Accra</h2>
                <p>We are a leading provider of innovative biogas solutions in Ghana, dedicated to helping communities, businesses, and agricultural sectors harness the power of organic waste. Our mission is to contribute to a sustainable environment by reducing emissions and providing affordable energy.</p>
                <a href="about.php" class="btn btn-outline-primary">Learn More About Us</a>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://via.placeholder.com/450x300.png?text=Biogas+Innovation" class="img-fluid rounded" alt="Biogas Innovation">
            </div>
        </div>
    </div>
</section>

<!-- Key Services Highlights for Homepage -->
<section id="home-services" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Our Core Services</h2>
        <div class="row">
            <div class="col-md-4 service-card">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/300x200.png?text=Domestic+Systems" class="card-img-top" alt="Domestic Biogas">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Domestic Biogas</h5>
                        <p class="card-text">Clean cooking gas and organic fertilizer for your home.</p>
                        <a href="services.php#domestic" class="btn btn-outline-primary mt-auto">Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 service-card">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/300x200.png?text=Commercial+Solutions" class="card-img-top" alt="Commercial Biogas">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Commercial & Industrial</h5>
                        <p class="card-text">Waste management and power generation for businesses.</p>
                        <a href="services.php#commercial" class="btn btn-outline-primary mt-auto">Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 service-card">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/300x200.png?text=Agricultural+Plants" class="card-img-top" alt="Agricultural Biogas">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Agricultural Biogas</h5>
                        <p class="card-text">Convert farm waste into energy and bio-fertilizer.</p>
                        <a href="services.php#agricultural" class="btn btn-outline-primary mt-auto">Details</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="services.php" class="btn btn-success btn-lg">Explore All Services</a>
        </div>
    </div>
</section>

<!-- Call to Action / Quick Contact Link -->
<section id="home-cta" class="py-5">
    <div class="container text-center">
        <h2>Ready to Embrace Sustainable Energy?</h2>
        <p class="lead">Let's discuss how biogas technology can benefit you or your organization.</p>
        <a href="contact.php" class="btn btn-primary btn-lg">Contact Us Today</a>
    </div>
</section>


<?php require_once 'includes/footer.php'; ?>
