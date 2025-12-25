<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'REMEI') }} - Agricultural Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary:   #FFB300;   /* Vibrant corn yellow */
            --secondary: #2E7D32;   /* Deep green (stalks) */
            --accent:    #FF6F00;   /* Harvest orange for buttons */
            --light-bg:  #FFF8E1;   /* Cream background */
            --text:      #1B5E20;   /* Dark green text */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--secondary) 0%, #1b5e20 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .landing-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            max-width: 1200px;
            width: 100%;
            background: var(--light-bg);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            min-height: 600px;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* LEFT SIDE - CONTENT */
        .content-side {
            background: linear-gradient(135deg, var(--secondary) 0%, #1b5e20 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 50px 40px;
            position: relative;
            overflow: hidden;
        }

        .content-side::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .content-side::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .content-inner {
            position: relative;
            z-index: 2;
            max-width: 450px;
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .logo-section {
            margin-bottom: 30px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            margin-bottom: 15px;
            color: var(--text);
            box-shadow: 0 8px 32px rgba(255, 179, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .company-name {
            font-size: 42px;
            font-weight: bold;
            margin: 0 0 8px 0;
            letter-spacing: 3px;
            color: var(--primary);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .company-tagline {
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 25px;
            opacity: 0.95;
            letter-spacing: 2px;
            color: #FFE082;
        }

        .company-description {
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 30px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.95);
        }

        .button-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .btn-custom {
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 2px solid var(--primary);
            flex: 1;
            min-width: 160px;
            justify-content: center;
        }

        .btn-website {
            background: var(--primary);
            color: var(--text);
        }

        .btn-website:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 111, 0, 0.4);
        }

        .btn-login {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .btn-login:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--text);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 179, 0, 0.4);
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            opacity: 0.9;
            color: rgba(255, 255, 255, 0.95);
        }

        .feature-item i {
            font-size: 16px;
            width: 28px;
            height: 28px;
            background: var(--primary);
            color: var(--text);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .footer-text {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 15px;
            color: rgba(255, 255, 255, 0.8);
        }

        /* RIGHT SIDE - IMAGE */
        .image-side {
            position: relative;
            overflow: hidden;
            animation: slideInRight 0.8s ease-out;
        }

        .carousel-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .carousel-slides {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .farm-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.05), rgba(255, 179, 0, 0.05));
            pointer-events: none;
        }

        .carousel-controls {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .carousel-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .carousel-dot.active {
            background: var(--primary);
            transform: scale(1.3);
        }

        .carousel-dot:hover {
            background: var(--primary);
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.3);
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .carousel-nav:hover {
            background: rgba(0, 0, 0, 0.6);
        }

        .carousel-nav.prev {
            left: 10px;
        }

        .carousel-nav.next {
            right: 10px;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .landing-container {
                grid-template-columns: 1fr;
                gap: 0;
                border-radius: 15px;
                min-height: auto;
            }

            .content-side {
                padding: 40px 30px;
            }

            .image-side {
                min-height: 350px;
                padding: 0;
            }
        }

        @media (max-width: 768px) {
            .landing-container {
                grid-template-columns: 1fr;
                border-radius: 15px;
            }

            .content-side {
                padding: 30px 20px;
            }

            .company-name {
                font-size: 32px;
            }

            .company-description {
                font-size: 13px;
                margin-bottom: 20px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-custom {
                flex: none;
                width: 100%;
            }

            .image-side {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <!-- LEFT SIDE - CONTENT -->
        <div class="content-side">
            <div class="content-inner">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="company-logo">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h1 class="company-name">REMEI</h1>
                    <p class="company-tagline">FARM MANAGEMENT SYSTEM</p>
                </div>

                <!-- Description -->
                <p class="company-description">
                    REMEI is a comprehensive digital platform designed to transform agricultural operations in Tanzania.
                    We empower farmers, coordinators, and managers with real-time data collection, analytics, and management tools
                    to optimize cotton and oil production.
                </p>

                <!-- Buttons -->
                <div class="button-group">
                    <a href="#" class="btn-custom btn-website" onclick="alert('Website coming soon!'); return false;">
                        <i class="fas fa-globe"></i> Website
                    </a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-custom btn-login">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-custom btn-login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        @endauth
                    @endif
                </div>

                <!-- Features -->
                <div class="features-list">
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Real-time Analytics</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-users-cog"></i>
                        <span>Multi-role Access</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-lock"></i>
                        <span>Secure & Encrypted</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Mobile Responsive</span>
                    </div>
                </div>

                <p class="footer-text">&copy; {{ date('Y') }} REMEI Tanzania Limited. All rights reserved.</p>
            </div>
        </div>

        <!-- RIGHT SIDE - COTTON FIELD IMAGE CAROUSEL -->
        <div class="image-side">
            <div class="carousel-container">
                <div class="carousel-slides" id="carouselSlides">
                    <!-- Slides will be added dynamically -->
                </div>

                <!-- Navigation Arrows -->
                <button class="carousel-nav prev" onclick="previousSlide()">&#10094;</button>
                <button class="carousel-nav next" onclick="nextSlide()">&#10095;</button>

                <!-- Carousel Dots -->
                <div class="carousel-controls" id="carouselDots">
                    <!-- Dots will be added dynamically -->
                </div>
            </div>
            <div class="image-overlay"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Carousel Images - using Laravel asset helper paths
        const carouselImages = [
            '{{ asset("images/cotton.jpg") }}',
            '{{ asset("images/cotton1.jpg") }}',
            '{{ asset("images/cotton3.jpg") }}',
            '{{ asset("images/cotton4.jpg") }}',
        ];

        let currentSlide = 0;
        let autoplayInterval;

        // Initialize carousel
        function initCarousel() {
            if (carouselImages.length === 0) return;

            const slidesContainer = document.getElementById('carouselSlides');
            const dotsContainer = document.getElementById('carouselDots');

            // Create slides
            carouselImages.forEach((image, index) => {
                const slideDiv = document.createElement('div');
                slideDiv.className = `carousel-slide ${index === 0 ? 'active' : ''}`;
                slideDiv.innerHTML = `<img src="${image}" alt="Farm image ${index + 1}" class="farm-image">`;
                slidesContainer.appendChild(slideDiv);

                // Create dot
                const dot = document.createElement('div');
                dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
                dot.onclick = () => goToSlide(index);
                dotsContainer.appendChild(dot);
            });

            // Auto-advance slides every 5 seconds
            startAutoplay();
        }

        function showSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.carousel-dot');

            if (index >= slides.length) {
                currentSlide = 0;
            } else if (index < 0) {
                currentSlide = slides.length - 1;
            } else {
                currentSlide = index;
            }

            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            clearInterval(autoplayInterval);
            showSlide(currentSlide + 1);
            startAutoplay();
        }

        function previousSlide() {
            clearInterval(autoplayInterval);
            showSlide(currentSlide - 1);
            startAutoplay();
        }

        function goToSlide(index) {
            clearInterval(autoplayInterval);
            showSlide(index);
            startAutoplay();
        }

        function startAutoplay() {
            autoplayInterval = setInterval(() => {
                showSlide(currentSlide + 1);
            }, 5000); // Change image every 5 seconds
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initCarousel);
    </script>
</body>
</html>
