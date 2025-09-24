<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Syntrex Software Solutions | Innovative IT and Software Services</title>
    <meta name="description" content="Syntrex Software Solutions offers cutting-edge IT services and innovative software solutions, transforming businesses with the latest technology.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="IT services, software solutions, innovative technology, business transformation, Syntrex Software Solutions">
    <meta name="author" content="Syntrex Software Solutions">
    <meta property="og:title" content="Syntrex Software Solutions | Innovative IT and Software Services">
    <meta property="og:description" content="Syntrex Software Solutions offers cutting-edge IT services and innovative software solutions, transforming businesses with the latest technology.">
    <meta property="og:url" content="https://www.syntrex.io">
    <meta name="twitter:title" content="Syntrex Software Solutions | Innovative IT and Software Services">
    <meta name="twitter:description" content="Syntrex Software Solutions offers cutting-edge IT services and innovative software solutions, transforming businesses with the latest technology.">

    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css?v=4">
    <link rel="stylesheet" href="assets/css/override.css">
</head>

<body>

    <style>
        


        /* NAV */
        .navbar {
            padding: .9rem 0;
            background: transparent;
            transition: all .3s ease
        }

        .navbar.scrolled {
            background: rgba(8, 12, 40, .9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25)
        }

        .navbar .nav-link {
            color: #e5e7eb !important;
            opacity: .9
        }

        .navbar .nav-link:hover {
            opacity: 1
        }

        .btn-quote {
            border-radius: 999px;
            font-weight: 600;
            background: linear-gradient(90deg, var(--accent), var(--accent-2));
            color: #fff;
            border: 0
        }

        /* HERO */
        .hero {
            position: relative;
            overflow: hidden;
            color: #fff;
            background:
                radial-gradient(1200px 600px at 10% -10%,
                    color-mix(in srgb, var(--accent-2) 25%, transparent),
                    transparent 60%),
                radial-gradient(1000px 600px at 110% 20%,
                    rgba(102, 126, 234, .18),
                    transparent 60%),
                linear-gradient(180deg,
                    var(--bg) 0%,
                    var(--primary) 100%);
        }

        .hero .btn.btn-outline-light.rounded-pill {
            background: transparent;
            border: 2px solid;
        }

        .hero .btn.btn-outline-light.rounded-pil:hover {
            background: #fff;
            color: #FF4495;
        }

        .hero-min {
            min-height: 100vh;
            display: flex;
            align-items: center
        }

        .hero h1 {
            font-weight: 700;
            letter-spacing: .3px;
            color: #fff;
        }

        .hero p.lead {
            color: #d1d5db;
            max-width: 560px;
            font-size: 16px;
        }

        /* Floating shapes */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            opacity: .35;
            mix-blend: screen
        }

        .orb.o1 {
            width: 180px;
            height: 180px;
            background: #5a8cff;
            top: 6%;
            left: 6%;
            animation: float 9s ease-in-out infinite
        }

        .orb.o2 {
            width: 260px;
            height: 260px;
            background: #ff7db3;
            top: 18%;
            right: 12%;
            animation: float 12s ease-in-out infinite
        }

        .orb.o3 {
            width: 120px;
            height: 120px;
            background: #7cf3ff;
            bottom: 8%;
            right: 30%;
            animation: float 10s ease-in-out infinite
        }

        @keyframes float {
            0% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-14px)
            }

            100% {
                transform: translateY(0)
            }
        }

        .hero-illustration {
            position: relative
        }

        .glass {
            position: absolute;
            inset: auto;
            right: 8%;
            top: 10%;
            width: 66%;
            height: 66%;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .04));
            border: 1px solid rgba(255, 255, 255, .25);
            box-shadow: 0 20px 60px rgba(14, 18, 52, .35)
        }

        .hero-card {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .25);
            backdrop-filter: blur(6px);
            border-radius: 1.25rem
        }

        /* SECTION UTILS */
        .section {
            padding: 90px 0
        }

        .section-muted {
            background: #f7f9fc
        }

        .eyebrow {
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 700;
            font-size: .85rem
        }

        .heading {
            font-weight: 700
        }

        .subtle {
            color: #6b7280
        }

        /* SERVICE ICONS */
        .service {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            border: 1px solid #eef2f7;
            box-shadow: 0 8px 30px rgba(17, 24, 39, .06);
            height: 100%
        }

        .service .icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #eff6ff, #fdf2f8);
            margin-bottom: 16px
        }

        /* Feature Block */
        .feature-card {
            border: 1px solid #eef2f7;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 8px 30px rgba(17, 24, 39, .06)
        }

        /* Stats */
        .stat {
            border-radius: 18px;
            border: 1px solid #eef2f7;
            background: #fff;
            box-shadow: 0 8px 30px rgba(17, 24, 39, .06);
            padding: 30px
        }

        .stat .value {
            font-size: 2rem;
            font-weight: 700
        }
    </style>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand text-white font-weight-bold" href="#">
                <img src="/assets/img/logo/syntrex-logo-white.png" class="main-logo">
            </a>
            <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse" data-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ml-auto align-items-lg-center">
                    <li class="nav-item mx-2"><a class="nav-link" href="/">Home</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="/services.php">Services</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="/about.php">About</a></li>
                    <li class="nav-item mx-2"><a class="nav-link" href="/contact.php">Contact</a></li>
                    <li class="nav-item ml-lg-3 mt-2 mt-lg-0"><a class="btn btn-quote" href="/contact.php">Get Free
                            Quote</a></li>
                </ul>
            </div>
        </div>
    </nav>
