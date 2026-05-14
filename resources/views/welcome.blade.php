<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCore Elite - Premium Fitness & Wellness Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            color: white;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .navbar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1.5rem 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.2rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: white !important;
        }

        .btn-login {
            background: white;
            color: #667eea;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: #667eea;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            max-width: 600px;
        }

        .btn-primary-custom {
            background: white;
            color: #667eea;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            color: #667eea;
        }

        .btn-secondary-custom {
            background: transparent;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid white;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary-custom:hover {
            background: white;
            color: #667eea;
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #e2e8f0;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }

        /* Stats Section */
        .stats {
            padding: 4rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta {
            padding: 5rem 0;
            background: white;
        }

        .cta-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 4rem;
            border-radius: 24px;
            text-align: center;
            color: white;
        }

        .cta-box h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-box p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        /* Footer */
        .footer {
            background: #1e293b;
            color: white;
            padding: 3rem 0 1.5rem;
        }

        .footer h5 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
            padding-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .cta-box {
                padding: 2rem;
            }

            .cta-box h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="#">
                    <div class="brand-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    FitCore Elite
                </a>
                <div class="d-flex align-items-center gap-3">
                    <a href="#features" class="nav-link d-none d-md-inline">Features</a>
                    <a href="#about" class="nav-link d-none d-md-inline">About</a>
                    <a href="{{ route('login') }}" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1>Transform Your Fitness Journey</h1>
                    <p>Join FitCore Elite and experience premium fitness management. Track your progress, manage memberships, and achieve your wellness goals.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-primary-custom">
                            Get Started <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="#features" class="btn btn-secondary-custom">
                            Learn More <i class="bi bi-chevron-down"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Active Members</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Services</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Access</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-title">
                <h2>Everything You Need</h2>
                <p>Comprehensive fitness management system designed for your success</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <h3>Membership Management</h3>
                        <p>Easy membership tracking with flexible plans and automated renewals. Stay on top of your fitness journey.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3>Attendance Tracking</h3>
                        <p>Quick check-in/check-out system. Monitor your workout frequency and build consistent habits.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <h3>Easy Payments</h3>
                        <p>Secure payment processing with multiple payment methods. Manage your billing effortlessly.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clipboard-pulse"></i>
                        </div>
                        <h3>Service Requests</h3>
                        <p>Request additional services like personal training, nutrition plans, and wellness programs.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Progress Tracking</h3>
                        <p>Monitor your fitness journey with detailed reports and insights into your activities.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>Secure & Reliable</h3>
                        <p>Your data is protected with industry-standard security measures and regular backups.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta" id="about">
        <div class="container">
            <div class="cta-box">
                <h2>Ready to Start Your Journey?</h2>
                <p>Join FitCore Elite today and take the first step towards a healthier, stronger you.</p>
                <a href="{{ route('login') }}" class="btn btn-primary-custom">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Access Your Account
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>FitCore Elite</h5>
                    <p class="text-white-50">Premium Fitness & Wellness Center. Your journey to better health starts here.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="#features">Features</a>
                        <a href="#about">About Us</a>
                        <a href="{{ route('login') }}">Member Login</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contact</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="mailto:info@fitcoreelite.com"><i class="bi bi-envelope me-2"></i>info@fitcoreelite.com</a>
                        <a href="tel:+1234567890"><i class="bi bi-telephone me-2"></i>+1 (234) 567-890</a>
                        <a href="#"><i class="bi bi-geo-alt me-2"></i>123 Fitness Street</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} FitCore Elite. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
