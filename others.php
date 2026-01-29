<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quick Solution</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Redesigned Services Page CSS */
        .container {
            max-width: 1400px;
            margin: 0 50px;
            padding: 20px 70px 5px 70px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 0 -10px;
        }

        .service-box {
            flex: 0 0 calc(33% - 20px);
            margin: 10px;
            padding: 20px 20px 5px 20px;
            
            background-color: #35231A;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
            color: #E0B65A;
            position: relative;
        }

        .service-box hr{
            color: gold;
            font-weight: 600;
            height: 2px;
        }

        .service-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .service-box img {
            width: 70px;
            height: 55px;
            margin-bottom: 15px;
        }

        .service-box .title {
            font-size: 18px;
            font-weight: 700;
            margin: 10px 0;
            color: #333;
        }

        .service-box p {
            font-size: 15px;
            color: #E0B65A;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .service-box .box-heading {
            font-size: 20px;
            font-weight: 500;
            color: #E0B65A;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .service-box a{
            text-decoration: none;
        }

        .service-box .learn-more {
            font-size: 12px;
            font-weight: 500;
            color: #E0B65A;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .service-box::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background-color: #007bff;
            border-radius: 12px 12px 0 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .service-box {
                flex: 0 0 calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .service-box {
                flex: 0 0 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar and Other Sections Omitted for Brevity -->

      <!-- Spinner Start -->
      <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    <!-- Topbar Start -->
    <div class="container-fluid bg-light p-0 wow fadeIn" data-wow-delay="0.1s">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-map-marker-alt text-primary me-2"></small>
                    <small>Baridhara, Gulshan-2, Dhaka-1212</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center py-3">
                    <small class="far fa-clock text-primary me-2"></small>
                    <small>Sat - Thu : 09.00 AM - 09.00 PM</small>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <div class="h-100 d-inline-flex align-items-center py-3 me-4">
                    <small class="fa fa-phone-alt text-primary me-2"></small>
                    <small>+88 01780337775</small>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-1" href=""><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-sm-square rounded-circle bg-white text-primary me-0" href=""><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0 wow fadeIn" data-wow-delay="0.1s">
    <a href="Homepage.php" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h1 class="m-0 text-primary"><i class="fas fa-landmark me-3"></i>Alliance</h1>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="Homepage.php" class="nav-item nav-link">Home</a>
            <a href="aboutus.php" class="nav-item nav-link ">About</a>
            <a href="others.php" class="nav-item nav-link active">expertise</a>
            <a href="mediator.php" class="nav-item nav-link">Mediator</a>
            <a href="arbitrator.php" class="nav-item nav-link">Arbitrator</a>
            <a href="querry.php" class="nav-item nav-link">Query</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Service</a>
                <div class="dropdown-menu rounded-0 rounded-bottom m-0">
                    <a href="Arbitration_proposal.php" class="dropdown-item">Arbitration Proposal</a>
                    <a href="Arbitration.php" class="dropdown-item">Arbitration Case File</a>
                    <a href="mediation_proposal.php" class="dropdown-item">Mediation Proposal</a>
                    <a href="mediation.php" class="dropdown-item">Mediation Case File</a>
                    <a href="lawyer.php" class="dropdown-item">Lawyers Info</a>
                    
                </div>
            </div>
            <a href="profile.php" class="nav-item nav-link ">Profile</a>
        </div>
        <a href="lawyer_registration.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Lawyer Registration</a>
    </div>
</nav>
    <!-- Navbar End -->

<style>
    .page-header {
    background: url("header-page.jpg") top center no-repeat;
    background-size: cover;
    text-shadow: 0 0 30px rgba(0, 0, 0, .1);
}
</style>
    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Our Expertise/Service Info</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Expertise</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->



    <!-- Services Page -->
    <div class="container">
        <div>
            <h2 style="text-align: center;">Our Service List</h2>
        </div>
        <br>
        <div class="row">
            <div class="service-box">
                <a href="1.php">
                <img src="img2/1.png" alt="Service 1 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                    
                <p>Alternative Dispute Resolution</p>
                <p>Arbitration & Mediation</p>
                <p class="learn-more">Learn More</p>
                </a>
            </div>

            <div class="service-box">
                <a href="2.php">
                <img src="img2/2.png" alt="Service 2 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p>Land Acqusition</p>
                <p><br></p>
                
                <p class="learn-more">Learn More </p>
                </a>
            </div>

            <div class="service-box">
                <a href="3.php">
                <img src="img2/3.png" alt="Service 3 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Commercial Leases</p>
                <p><br></p>
                
                <p class="learn-more">Learn More</p>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="service-box">
                <a href="4.php">
                <img src="img2/4.png" alt="Service 4 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Real Estate</p>

                <p>Sale,Purchase & Refinance</p>
                <p class="learn-more">Learn More</p>
                </a>
            </div>

            <div class="service-box">
                <a href="5.php">
                <img src="img2/5.png" alt="Service 5 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Re-development</p>
                <p><br></p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>

            <div class="service-box">
                <a href="6.php">
                <img src="img2/6.png" alt="Service 6 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Devorce / Separation</p>
                <p><br></p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="service-box">
                <a href="7.php">
                <img src="img2/7.png" alt="Service 4 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Adoption</p>
                <p><br></p>
                <p class="learn-more">Learn More</p>
                </a>
            </div>

            <div class="service-box">
                <a href="8.php">
                <img src="img2/8.png" alt="Service 5 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Wills And Estates</p>
                <p>Personal Directives, Power of Attorney</p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>

            <div class="service-box">
                <a href="9.php">
                <img src="img2/9.png" alt="Service 6 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Division of Family</p>
                <p>Property</p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="service-box">
                <a href="10.php">
                <img src="img2/10.png" alt="Service 4 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Legal Opinion & Litigation</p>
                <p><br></p>
                <p class="learn-more">Learn More</p>
                </a>
            </div>

            <div class="service-box">
                <a href="11.php">
                <img src="img2/11.png" alt="Service 5 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Joint Development Agreement</p>
                <p><br></p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>

            <div class="service-box">
                <a href="12.php">
                <img src="img2/12.png" alt="Service 6 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> IT Park.</p>
                <p><br></p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="service-box">
                <a href="13.php">
                <img src="img2/13.png" alt="Service 4 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> General Documentation</p>
                <p><br></p>
                <p class="learn-more">Learn More</p>
                </a>
            </div>

            <div class="service-box">
                <a href="14.php">
                <img src="img2/14.png" alt="Service 5 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p>Advisory, Due Diligence</p>
                <p>& Title Investigation</p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>

            <div class="service-box">
                <a href="15.php">
                <img src="img2/15.png" alt="Service 6 Icon">
                <hr>
                <p class="box-heading">Alliacce</p>
                <p> Business Acqusition</p>
                <p>Mergers & Demergers</p>
                <p class="learn-more">Learn More </p>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer and Other Sections Omitted for Brevity -->

        <!-- Footer Start -->
        <div class="container-fluid bg-dark text-light footer mt-5 pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Address</h5>
                    <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Baridhara, Gulshan-2, Dhaka-1212</p>
                    <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+88 01780337775</p>
                    <p class="mb-2"><i class="fa fa-envelope me-3"></i>quicksolution@gmail.com</p>
                    <div class="d-flex pt-2">
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-youtube"></i></a>
                        <a class="btn btn-outline-light btn-social rounded-circle" href=""><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Services</h5>
                    <a class="btn btn-link" href="">Arbitration</a>
                    <a class="btn btn-link" href="">Mediation</a>
                    <a class="btn btn-link" href="">Lawyers</a>
                    <a class="btn btn-link" href="">Arbitration case file</a>
                    <a class="btn btn-link" href="">Mediation Case File</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Quick Links</h5>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <!-- most right text area-->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Newsletter</h5>
                    <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
                    <div class="position-relative mx-auto" style="max-width: 400px;">
                        <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
                <!-- end of most right text area-->
            </div>
        </div>
    
    </div>
    <!-- Footer End -->



    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>



    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
