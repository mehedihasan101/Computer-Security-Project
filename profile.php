<?php
session_start();
include("db.php");

// Check if user is logged in
if (isset($_SESSION['user_email'])) {
    $userprofile = $_SESSION['user_email'];

    // Query for user information
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $userprofile);
    $stmt->execute();
    $result = $stmt->get_result();

    // Query for lawyer information if the user is a lawyer
    $stmt2 = $conn->prepare("SELECT l.lawyer_id as lawyer_id
          FROM user AS u 
          JOIN lawyer as l ON u.email = l.email
          WHERE u.status = 'lawyer' and u.email = ?");
    $stmt2->bind_param("s", $userprofile);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $value = 0;
    if ($result2 && $result2->num_rows > 0) {
        $data2 = $result2->fetch_assoc();
        $value = $data2['lawyer_id'];
    }

    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        echo "No user data found.";
        exit;
    }
} else {
    echo "User not logged in. Please <a href='login.php'>login</a>.";
    exit;
}

// Close connection
$conn->close();
?>

<!-- HTML START -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quick Solution</title>
    <style>
        .logout-button, .edit-profile, .edit-professional-profile, .appointment-request, .appointment-approval {
            height: 40px;
            padding: 0 20px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .logout-button {
            background-color: #dc3545;
        }

        .logout-button:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .edit-profile {
            background-color: #007bff;
        }

        .edit-profile:hover {
            background-color: #0069d9;
            transform: scale(1.05);
        }

        .edit-professional-profile {
            background-color: #28a745;
        }

        .edit-professional-profile:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .appointment-request {
            background-color: #ff5733;
        }

        .appointment-request:hover {
            background-color: #e04e29;
            transform: scale(1.05);
        }

        .appointment-approval {
            background-color: #17a2b8;
        }

        .appointment-approval:hover {
            background-color: #138496;
            transform: scale(1.05);
        }
    </style>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link href="img/favicon.ico" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
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
                <a href="aboutus.php" class="nav-item nav-link">About</a>
                <a href="others.php" class="nav-item nav-link">Expertise</a>
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
                <a href="profile.php" class="nav-item nav-link active">Profile</a>
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">My Profile</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">My Profile</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Profile Section -->
    <section style="background-color: #eee;">
        <div class="container py-5">
            <div class="row">
                <!-- Profile left side -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <?php
                            echo "<img src='" . $data['profilepic'] . "' height='100px' width='100px'>";
                            ?>
                            <p class="text-muted mb-1">A respected Client</p>
                            <p class="text-muted mb-4">Thanks for believing in us</p>

                            <div class="d-flex justify-content-center mb-2">
                                <?php 
                                if ($data['status'] == 'lawyer') {
                                    echo '<a class="edit-professional-profile" style="padding-top:7px" href="professional_profile.php?lawyer_id=' . htmlspecialchars($value) . '">Edit Professional Profile</a>';
                                }
                                ?>
                            </div>

                            <div class="d-flex justify-content-center mb-2">
                                <a class="edit-profile" style="padding-top:7px" href="edit_profile.php">Edit Profile</a>
                            </div>

                            <div class="d-flex justify-content-center mb-2">
                                <a class="logout-button" style="padding-top:7px" href="logout.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile details right side -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Full Name</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $data['fullname']; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Email</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $data['email']; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Phone</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $data['phone']; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Status</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $data['status']; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">My Note</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $data['note']; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-center mb-2">
                            <a class="edit-profile" style="padding-top:7px" href="history.php">View History</a>
                            <?php if ($data['status'] == 'lawyer'): ?>
                                <a class="appointment-approval" style="padding-top:7px; margin-left: 10px;" href="lawyer_approval.php?lawyer_id=<?php echo htmlspecialchars($value); ?>">Appointment Approval</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Profile Section End -->

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
    <script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Contact Javascript File -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Main Javascript File -->
    <script src="js/main.js"></script>
</body>
</html>
