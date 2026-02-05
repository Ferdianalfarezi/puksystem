<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-mono:400,700|epilogue:400,500,600,700,800" rel="stylesheet" />

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --black: #0a0a0a;
                --white: #fafafa;
                --gray: #888888;
                --accent: #ff3366;
                --spacing-xs: 0.5rem;
                --spacing-sm: 1rem;
                --spacing-md: 2rem;
                --spacing-lg: 4rem;
                --spacing-xl: 6rem;
            }

            body {
                font-family: 'Epilogue', sans-serif;
                background: var(--black);
                color: var(--white);
                overflow-x: hidden;
                line-height: 1.6;
            }

            /* Navigation */
            .nav {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1000;
                padding: var(--spacing-sm) var(--spacing-md);
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: rgba(245, 245, 245, 0);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0);
                animation: slideDown 0.6s ease-out;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .logo {
                font-family: 'Space Mono', monospace;
                font-size: 1.5rem;
                font-weight: 700;
                letter-spacing: -0.02em;
            }

            .nav-links {
                display: flex;
                gap: var(--spacing-md);
                list-style: none;
            }

            .nav-links a {
                color: var(--white);
                text-decoration: none;
                font-size: 0.95rem;
                font-weight: 500;
                padding: 0.5rem 1.25rem;
                border: 1px solid transparent;
                border-radius: 50px;
                transition: all 0.3s ease;
            }

            .nav-links a:hover {
                border-color: var(--white);
                background: rgba(255, 255, 255, 0.05);
            }

            .nav-links a.primary {
                background: var(--white);
                color: var(--black);
                border-color: var(--white);
            }

            .nav-links a.primary:hover {
                background: var(--accent);
                border-color: var(--accent);
                color: var(--white);
            }

            /* Hero Section */
            .hero {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: var(--spacing-xl) var(--spacing-md);
                padding-top: calc(var(--spacing-xl) + 60px);
                position: relative;
                overflow: hidden;
                background-image: url('{{ asset("images/landingbg.png") }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            }

            .hero::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: 
                    linear-gradient(to bottom, rgba(255, 255, 255, 0)gba(10, 10, 10, 0.85)),
                    radial-gradient(circle at 20% 30%, rgba(255, 51, 102, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
                pointer-events: none;
            }

            .hero-content {
                max-width: 1200px;
                text-align: center;
                position: relative;
                z-index: 1;
            }

            .hero-tag {
                display: inline-block;
                padding: 0.5rem 1.5rem;
                background: rgba(255, 255, 255, 0);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 50px;
                font-size: 0.875rem;
                font-weight: 600;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                margin-bottom: var(--spacing-md);
                animation: fadeInUp 0.8s ease-out 0.2s both;
                backdrop-filter: blur(10px);
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .hero h1 {
                font-size: clamp(3rem, 10vw, 7rem);
                font-weight: 800;
                line-height: 1;
                margin-bottom: var(--spacing-md);
                letter-spacing: -0.03em;
                animation: fadeInUp 0.8s ease-out 0.4s both;
                text-shadow: 0 2px 20px rgba(0, 0, 0, 0.5);
            }

            .hero h1 .accent {
                background: linear-gradient(135deg, #ff3366 0%, #ff6b9d 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero p {
                font-size: clamp(1.125rem, 2vw, 1.5rem);
                color: var(--white);
                max-width: 700px;
                margin: 0 auto var(--spacing-lg);
                animation: fadeInUp 0.8s ease-out 0.6s both;
                text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            }

            .hero-cta {
                display: flex;
                gap: var(--spacing-sm);
                justify-content: center;
                flex-wrap: wrap;
                animation: fadeInUp 0.8s ease-out 0.8s both;
            }

            .btn {
                padding: 1rem 2.5rem;
                font-size: 1.125rem;
                font-weight: 600;
                text-decoration: none;
                border-radius: 50px;
                border: 2px solid;
                transition: all 0.3s ease;
                display: inline-block;
                cursor: pointer;
            }

            .btn-primary {
                background: var(--white);
                color: var(--black);
                border-color: var(--white);
            }

            .btn-primary:hover {
                background: var(--accent);
                border-color: var(--accent);
                color: var(--white);
                transform: translateY(-2px);
                box-shadow: 0 10px 40px rgba(255, 51, 102, 0.3);
            }

            .btn-secondary {
                background: rgba(255, 255, 255, 0.1);
                color: var(--white);
                border-color: rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(10px);
            }

            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.2);
                border-color: var(--white);
                transform: translateY(-2px);
            }

            /* Laravel Logo Section */
            .logo-section {
                margin: var(--spacing-xl) 0;
                padding: var(--spacing-xl) var(--spacing-md);
                text-align: center;
                position: relative;
            }

            .logo-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 1px;
                height: 60px;
                background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.2), transparent);
            }

            .laravel-logo-container {
                max-width: 600px;
                margin: 0 auto;
                padding: var(--spacing-lg);
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-20px);
                }
            }

            .laravel-logo-container svg {
                width: 100%;
                height: auto;
                filter: drop-shadow(0 10px 40px rgba(255, 51, 102, 0.2));
            }

            /* News Carousel */
            .news-section {
                padding: var(--spacing-xl) var(--spacing-md);
                background: var(--white);
                color: var(--black);
                position: relative;
            }

            .news-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(to right, transparent, var(--black), transparent);
            }

            .section-header {
                max-width: 1200px;
                margin: 0 auto var(--spacing-lg);
                text-align: center;
            }

            .section-header h2 {
                font-size: clamp(2.5rem, 5vw, 4rem);
                font-weight: 800;
                letter-spacing: -0.02em;
                margin-bottom: var(--spacing-sm);
            }

            .section-header p {
                font-size: 1.25rem;
                color: var(--gray);
            }

            .carousel-container {
                max-width: 1400px;
                margin: 0 auto;
                position: relative;
                overflow: hidden;
            }

            .carousel-track {
                display: flex;
                gap: var(--spacing-md);
                transition: transform 0.5s ease;
            }

            .news-card {
                min-width: 380px;
                background: var(--black);
                color: var(--white);
                border-radius: 16px;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                cursor: pointer;
            }

            .news-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }

            .news-card-image {
                width: 100%;
                height: 240px;
                background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }

            .news-card-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .news-card:hover .news-card-image img {
                transform: scale(1.05);
            }

            .news-card-image::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
                z-index: 1;
            }

            .news-card-content {
                padding: var(--spacing-md);
            }

            .news-category {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                background: var(--accent);
                color: var(--white);
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                border-radius: 4px;
                margin-bottom: var(--spacing-sm);
            }

            .news-card h3 {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: var(--spacing-xs);
                line-height: 1.3;
            }

            .news-card p {
                color: var(--gray);
                margin-bottom: var(--spacing-sm);
                line-height: 1.6;
            }

            .news-date {
                font-size: 0.875rem;
                color: var(--gray);
                font-family: 'Space Mono', monospace;
            }

            .carousel-controls {
                display: flex;
                justify-content: center;
                gap: var(--spacing-sm);
                margin-top: var(--spacing-lg);
            }

            .carousel-btn {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: 2px solid var(--black);
                background: transparent;
                color: var(--black);
                font-size: 1.25rem;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .carousel-btn:hover {
                background: var(--black);
                color: var(--white);
                transform: scale(1.1);
            }

            .carousel-dots {
                display: flex;
                justify-content: center;
                gap: var(--spacing-xs);
                margin-top: var(--spacing-md);
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: rgba(0, 0, 0, 0.2);
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .dot.active {
                background: var(--black);
                width: 30px;
                border-radius: 5px;
            }

            /* Footer */
            .footer {
                padding: var(--spacing-xl) var(--spacing-md);
                background: var(--black);
                text-align: center;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .footer p {
                color: var(--gray);
                font-size: 0.875rem;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .nav-links {
                    gap: var(--spacing-sm);
                }

                .nav-links a {
                    padding: 0.4rem 1rem;
                    font-size: 0.85rem;
                }

                .hero {
                    padding-top: calc(var(--spacing-lg) + 60px);
                    background-attachment: scroll;
                }

                .hero-cta {
                    flex-direction: column;
                    align-items: center;
                }

                .btn {
                    width: 100%;
                    max-width: 300px;
                }

                .news-card {
                    min-width: 300px;
                }

                .carousel-track {
                    padding: 0 var(--spacing-sm);
                }
            }

            @media (max-width: 480px) {
                .logo {
                    font-size: 1.25rem;
                }

                .news-card {
                    min-width: 280px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Navigation -->
        <nav class="nav">
            <div class="logo"></div>
            @if (Route::has('login'))
                <ul class="nav-links">
                     <li><a href="{{ route('login') }}">Log in</a></li>
                    
                </ul>
            @endif
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                {{-- <span class="hero-tag">The PHP Framework</span>
                <h1>Build Something <span class="accent">Amazing</span></h1>
                <p>Laravel makes it easy to build modern, powerful web applications with expressive, elegant syntax.</p>
                <div class="hero-cta">
                    <a href="https://laravel.com/docs" class="btn btn-primary" target="_blank">Get Started</a>
                    <a href="https://laracasts.com" class="btn btn-secondary" target="_blank">Learn Laravel</a>
                </div> --}}
            </div>
        </section>

        

        <!-- News Carousel Section -->
        <section class="news-section">
            <div class="section-header">
                <h2>Latest News</h2>
                <p>Stay updated with the latest from the Laravel ecosystem</p>
            </div>

            <div class="carousel-container">
                <div class="carousel-track" id="carouselTrack">
                    <!-- News Card 1 -->
                    <div class="news-card">
                        <div class="news-card-image">
                            <img src="{{ asset('images/news1.jpg') }}" alt="Laravel 11 Released">
                        </div>
                        <div class="news-card-content">
                            <span class="news-category">Release</span>
                            <h3>Laravel 11 Released</h3>
                            <p>Discover the newest features and improvements in Laravel 11, including streamlined application structure and enhanced performance.</p>
                            <span class="news-date">March 2024</span>
                        </div>
                    </div>

                    <!-- News Card 2 -->
                    <div class="news-card">
                        <div class="news-card-image">
                            <img src="{{ asset('images/news2.jpg') }}" alt="Reverb Real-time Broadcasting">
                        </div>
                        <div class="news-card-content">
                            <span class="news-category">Feature</span>
                            <h3>Reverb: Real-time Broadcasting</h3>
                            <p>Laravel Reverb brings blazing-fast, scalable WebSocket server directly to your Laravel application.</p>
                            <span class="news-date">February 2024</span>
                        </div>
                    </div>

                    <!-- News Card 3 -->
                    <div class="news-card">
                        <div class="news-card-image">
                            <img src="{{ asset('images/news3.jpg') }}" alt="New Laracasts Series">
                        </div>
                        <div class="news-card-content">
                            <span class="news-category">Education</span>
                            <h3>New Laracasts Series</h3>
                            <p>Learn advanced Laravel techniques with our comprehensive new video series covering everything from basics to expert level.</p>
                            <span class="news-date">January 2024</span>
                        </div>
                    </div>

                    <!-- News Card 4 -->
                    <div class="news-card">
                        <div class="news-card-image">
                            <img src="{{ asset('images/news4.jpg') }}" alt="Laravel Cloud Updates">
                        </div>
                        <div class="news-card-content">
                            <span class="news-category">Cloud</span>
                            <h3>Laravel Cloud Updates</h3>
                            <p>Deploy your Laravel applications faster than ever with enhanced CI/CD pipelines and automatic scaling.</p>
                            <span class="news-date">December 2023</span>
                        </div>
                    </div>

                    <!-- News Card 5 -->
                    <div class="news-card">
                        <div class="news-card-image">
                            <img src="{{ asset('images/news5.jpg') }}" alt="Laravel Herd 2.0">
                        </div>
                        <div class="news-card-content">
                            <span class="news-category">Tools</span>
                            <h3>Laravel Herd 2.0</h3>
                            <p>The fastest way to develop Laravel applications locally just got better with improved performance and new features.</p>
                            <span class="news-date">November 2023</span>
                        </div>
                    </div>
                </div>

                <div class="carousel-controls">
                    <button class="carousel-btn" id="prevBtn">←</button>
                    <button class="carousel-btn" id="nextBtn">→</button>
                </div>

                <div class="carousel-dots" id="carouselDots"></div>
            </div>
        </section>

        {{-- <!-- Laravel Logo Section -->
        <section class="logo-section">
            <div class="laravel-logo-container">
                <svg viewBox="0 0 440 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="#FF3366"/>
                    <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337Z" fill="white"/>
                    <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337Z" fill="white"/>
                    <path d="M438 -3H421.694V102.197H438V-3Z" fill="#FF3366"/>
                    <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="white"/>
                    <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="white"/>
                    <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656Z" fill="white"/>
                </svg>
            </div>
        </section> --}}

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} Laravel. All rights reserved. Built with ❤️ by the Laravel community.</p>
        </footer>

        <script>
            // Carousel functionality
            const track = document.getElementById('carouselTrack');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const dotsContainer = document.getElementById('carouselDots');
            const cards = document.querySelectorAll('.news-card');
            
            let currentIndex = 0;
            const cardWidth = 380 + 32; // card width + gap
            const totalCards = cards.length;
            const visibleCards = window.innerWidth > 768 ? 3 : 1;
            const maxIndex = Math.max(0, totalCards - visibleCards);

            // Create dots
            for (let i = 0; i <= maxIndex; i++) {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }

            const dots = document.querySelectorAll('.dot');

            function updateCarousel() {
                const offset = -currentIndex * cardWidth;
                track.style.transform = `translateX(${offset}px)`;
                
                // Update dots
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }

            function goToSlide(index) {
                currentIndex = Math.max(0, Math.min(index, maxIndex));
                updateCarousel();
            }

            prevBtn.addEventListener('click', () => {
                goToSlide(currentIndex - 1);
            });

            nextBtn.addEventListener('click', () => {
                goToSlide(currentIndex + 1);
            });

            // Auto-play carousel
            let autoplayInterval = setInterval(() => {
                if (currentIndex >= maxIndex) {
                    currentIndex = 0;
                } else {
                    currentIndex++;
                }
                updateCarousel();
            }, 5000);

            // Pause autoplay on hover
            track.addEventListener('mouseenter', () => {
                clearInterval(autoplayInterval);
            });

            track.addEventListener('mouseleave', () => {
                autoplayInterval = setInterval(() => {
                    if (currentIndex >= maxIndex) {
                        currentIndex = 0;
                    } else {
                        currentIndex++;
                    }
                    updateCarousel();
                }, 5000);
            });

            // Handle resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    location.reload();
                }, 250);
            });

            // Smooth scroll
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