<?php
// Include database connection
include 'db.php';

// Initialize search term variable
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch lawyers from the database based on the search term
$query = "SELECT u.email as email, l.date, l.lawyer_id, l.full_name as full_name, l.catagory AS specialization, l.court as court, l.qualification,
 u.profilepic as profilepic, l.fee as fee
          FROM user AS u 
          JOIN lawyer as l ON u.email = l.email
          WHERE u.status = 'lawyer'";

// Add search term filter if it's set
if ($searchTerm != '') {
    $query .= " AND (l.court LIKE '%$searchTerm%' OR CONCAT(l.full_name, u.email) LIKE '%$searchTerm%')";
}

// Execute query
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <title>Alliance </title>
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

    <!-- xtra team link start -->
    
    <style>
        /* Custom animation for lawyer cards */
        .lawyer-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .lawyer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        /* Increase circle size, border, and padding for images */
        .lawyer-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 5px solid #007bff;
            padding: 7px;
        }
    </style>
</head>

<body>
    
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
    <!-- Topbar End -->


    <!-- Navbar Start -->
    
    
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
            <a href="others.php" class="nav-item nav-link ">expertise</a>
            <a href="mediator.php" class="nav-item nav-link">Mediator</a>
            <a href="arbitrator.php" class="nav-item nav-link">Arbitrator</a>
            <a href="querry.php" class="nav-item nav-link">Query</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Service</a>
                <div class="dropdown-menu rounded-0 rounded-bottom m-0">
                    <a href="Arbitration_proposal.php" class="dropdown-item">Arbitration Proposal</a>
                    <a href="Arbitration.php" class="dropdown-item">Arbitration Case File</a>
                    <a href="mediation_proposal.php" class="dropdown-item">Mediation Proposal</a>
                    <a href="mediation.php" class="dropdown-item">Mediation Case File</a>
                    <a href="lawyer.php" class="dropdown-item">Lawyers Info</a>
                    
                </div>
            </div>
            <a href="profile.php" class="nav-item nav-link">Profile</a>
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">Lawyer Information</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Lawyer Info</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->
    <!-- Search Bar Start -->
    <div class="container mb-5">
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search for a lawyer by name or court" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>
    <!-- Search Bar End -->

    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5">
                <p class="d-inline-block border rounded-pill py-1 px-4">Lawyers</p>
                <h1>Our Experienced Lawyers</h1>
            </div>

            <!-- Lawyer cards -->
            <div class="row g-4">
                <?php
                // Check if there are lawyers in the result
                if (mysqli_num_rows($result) > 0) {
                    while ($lawyer = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="col-lg-3 col-md-6 wow fadeInUp lawyer-card" data-wow-delay="0.2s">
                            <div class="card border-0 shadow-sm h-100 text-center p-4">
                                <div class="card-body">
                                    <!-- Add image in the card with larger circle, border, and padding -->
                                    <?php echo "<img class='img-fluid rounded-circle mb-4 lawyer-image' src='".$lawyer['profilepic']."'>"; ?>
                                    <h6 class="card-title text-black"><?php echo htmlspecialchars($lawyer['full_name']); ?></h6>
                                    <p class="card-text text-black">Category: <?php echo htmlspecialchars($lawyer['specialization']); ?></p>
                                    <p class="card-text">Court: <?php echo htmlspecialchars($lawyer['court']); ?></p>
                                    <p class="card-text">Fee: <?php echo htmlspecialchars($lawyer['fee']); ?></p>
                                    <a href="appointment.php?lawyer_id=<?php echo htmlspecialchars($lawyer['lawyer_id']); ?>" class="btn btn-primary rounded-pill">Book Appointment</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center'>No lawyers found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Team End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-dark text-light footer pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Address</h5>
                    <p><i class="fa fa-map-marker-alt me-3"></i>123 Street, New York, USA</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                    <p><i class="fa fa-envelope me-3"></i>info@example.com</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Quick Links</h5>
                    <a class="btn btn-link" href="">About Us</a>
                    <a class="btn btn-link" href="">Contact Us</a>
                    <a class="btn btn-link" href="">Our Services</a>
                    <a class="btn btn-link" href="">Terms & Condition</a>
                    <a class="btn btn-link" href="">Support</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Newsletter</h5>
                    <p>Dolor amet sit justo amet elitr clita ipsum elitr est.</p>
                    <div class="position-relative w-100">
                        <input class="form-control border-0 rounded-pill w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
                        <button type="button" class="btn btn-primary rounded-pill py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Follow Us</h5>
                    <div class="d-flex">
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href="#"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href="#"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-square btn-outline-light rounded-circle me-2" href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
