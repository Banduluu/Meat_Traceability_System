<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meat Country of Origin Label</title>
    <link rel="stylesheet" href="index_style.css">
</head>
<body>
    <!-- Navigation Header -->
    <header class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="logo">CooL</a>
            <nav class="nav-menu">
                <a href="#home" class="nav-link">Home</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <!-- Left Content -->
            <div class="hero-left">
                <h1 class="hero-title">Meat Country of Origin Label</h1>
                <p class="hero-subtitle">Lipa City Slaughter House</p>
                <div class="button-group">
                    <button class="btn btn-register">
                        <span class="btn-icon">👤</span> Register Now
                    </button>
                    <button class="btn btn-signin">
                        <span class="btn-icon">🔐</span> Sign In
                    </button>
                </div>
            </div>

            <!-- Right Image -->
            <div class="hero-right">
                <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&h=500&fit=crop" alt="Road through forest" class="hero-image">
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="site-section">
        <div class="section-inner">
            <h2 class="section-title">About</h2>
            <p class="section-lead">Lipa City Slaughterhouse was formerly managed by the LGU, before it was turned over to COMNET MANAGEMENT CORPORATION to handle its operation last December 2013 and was later manage by Comnet sister Company, First Manhattan Integrated Management Corporation. The Plant occupies approximately 8 hectares which is currently on lease contract with the LGU, while the buildings and all the equipment and machineries are all privately own. Last 2019, FMIMC becomes the first and only “AAA” certified slaughterhouse in Batangas.</p>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="site-section">
        <div class="section-inner">
            <h2 class="section-title">Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>Label Generation</h3>
                    <p>Create compliant country-of-origin labels with QR codes and detailed origin data.</p>
                </div>
                <div class="service-card">
                    <h3>Traceability</h3>
                    <p>Track consignments from source to market with timestamps and handlers recorded.</p>
                </div>
                <div class="service-card">
                    <h3>Consulting</h3>
                    <p>Regulatory guidance and onboarding support for small and medium processors.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="site-section">
        <div class="section-inner">
            <h2 class="section-title">Contact</h2>
            <p class="section-lead">Have questions or need help implementing labels? Send us a message.</p>
            <form class="contact-form" action="#" method="post">
                <div class="form-row full">
                    <input type="text" name="name" placeholder="Your name" required>
                </div>
                <div class="form-row full">
                    <input type="email" name="email" placeholder="Your email" required>
                </div>
                <div class="form-row full">
                    <textarea name="message" rows="5" placeholder="Message" required></textarea>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-register">Send Message</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-inner">
            <p>&copy; 2026 CooL. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
