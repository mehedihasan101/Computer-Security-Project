<?php  
session_start();

// --- Security Headers ---
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// --- Check if logged in ---
if (empty($_SESSION['user_email'])) {
    echo '<script>alert("Please login first."); window.location.href="Login.php";</script>';
    exit;
}



// --- Database Connection ---
$host = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'adr';
$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_email = $_SESSION['user_email'];

// --- Fetch user data securely ---
$stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows === 0) {
    echo '<script>alert("User not found. Please login again."); window.location.href="Login.php";</script>';
    exit;
}

$data = $result->fetch_assoc();
$stmt->close();

// --- Extract values safely ---
$email = htmlspecialchars($data['email']);
$phone = htmlspecialchars($data['phone']);
$full_name = htmlspecialchars($data['fullname']);
$role = htmlspecialchars($data['status']);

// Check user role from session (based on your login system)
if ($role === 'lawyer') {
    echo '<script>alert("You are already registered as a lawyer. You cannot register again."); window.location.href="Profile.php";</script>';
    exit;
}

// ✅ Check if user has approved lawyer status in lawyer table
$lawyer_check_stmt = $conn->prepare("SELECT status FROM lawyer WHERE email = ? AND status = 'approved'");
$lawyer_check_stmt->bind_param("s", $email);
$lawyer_check_stmt->execute();
$lawyer_check_result = $lawyer_check_stmt->get_result();

if ($lawyer_check_result->num_rows > 0) {
    echo '<script>alert("You are already registered as a lawyer. You cannot register again."); window.location.href="Profile.php";</script>';
    exit;
}
$lawyer_check_stmt->close();

// ✅ Check if user has pending application in lawyer table
$pending_check_stmt = $conn->prepare("SELECT status FROM lawyer WHERE email = ? AND status = 'pending'");
$pending_check_stmt->bind_param("s", $email);
$pending_check_stmt->execute();
$pending_check_result = $pending_check_stmt->get_result();

if ($pending_check_result->num_rows > 0) {
    echo '<script>alert("You have already submitted a lawyer registration form. Please wait for admin approval."); window.location.href="Lawyer.php";</script>';
    exit;
}
$pending_check_stmt->close();

// --- Handle Form Submission ---
if (isset($_POST['save'])) {
    $qualification = trim($_POST['qualification']);
    $lawyer_id = trim($_POST['lawyer_id']);
    $chamber_address = trim($_POST['chamber_address']);
    $category = trim($_POST['catagory']);
    $court = trim($_POST['court']);
    $date = date('F d Y, h:i:s A');

    // --- Validate Required Fields ---
    if (empty($qualification) || empty($lawyer_id) || empty($chamber_address) || empty($category) || empty($court)) {
        echo '<script>alert("All fields are required."); window.location.href="lawyer_registration.php";</script>';
        exit;
    }

    // --- Check if lawyer_id already exists ---
    $check_stmt = $conn->prepare("SELECT lawyer_id FROM lawyer WHERE lawyer_id = ?");
    $check_stmt->bind_param("s", $lawyer_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo '<script>alert("This Lawyer ID already exists. Please use a unique ID."); window.location.href="lawyer_registration.php";</script>';
        exit;
    }
    $check_stmt->close();

    // --- Insert new record ---
    $insert_stmt = $conn->prepare("INSERT INTO lawyer (full_name, email, qualification, lawyer_id, phone, chamber_address, date, status, court, catagory) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)");
    $insert_stmt->bind_param("sssssssss", $full_name, $email, $qualification, $lawyer_id, $phone, $chamber_address, $date, $court, $category);

    if ($insert_stmt->execute()) {
        echo '<script>alert("Form submitted successfully. Awaiting admin approval."); window.location.href="Profile.php";</script>';
    } else {
        echo '<script>alert("Error submitting form. Please try again later."); window.location.href="lawyer_registration.php";</script>';
    }

    $insert_stmt->close();
}

$conn->close();
?>



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
            <a href="aboutus.php" class="nav-item nav-link active">About</a>
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">Lawyer registration</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Register</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Appointment Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <p class="d-inline-block border rounded-pill py-1 px-4">registration</p>
                    <h1 class="mb-4">Submit the form for registration as a lawyer</h1>
                    <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                    <div class="bg-light rounded d-flex align-items-center p-5 mb-4">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                            <i class="fa fa-phone-alt text-primary"></i>
                        </div>
                        <div class="ms-4">
                            <p class="mb-2">Call Us Now</p>
                            <h5 class="mb-0">+88 01780337775</h5>
                        </div>
                    </div>
                    <div class="bg-light rounded d-flex align-items-center p-5">
                        <div class="d-flex flex-shrink-0 align-items-center justify-content-center rounded-circle bg-white" style="width: 55px; height: 55px;">
                            <i class="fa fa-envelope-open text-primary"></i>
                        </div>
                        <div class="ms-4">
                            <p class="mb-2">Mail Us Now</p>
                            <h5 class="mb-0">info@example.com</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 d-flex align-items-center p-5">
                        

                        <form method="POST" action="lawyer_registration.php">
    <div class="row g-3">
        <div class="col-12 col-sm-6">
            <input type="text" class="form-control border-0" name="full_name" value="<?php echo $full_name; ?>" style="height: 55px;" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="email" class="form-control border-0" name="email" value="<?php echo $email; ?>" style="height: 55px;" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="form-control border-0" name="phone" value="<?php echo $phone; ?>" style="height: 55px;" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="form-control border-0" name="lawyer_id" placeholder="Lawyer ID" style="height: 55px;" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="form-control border-0" name="catagory" placeholder="Category" style="height: 55px;" required>
        </div>
        <div class="col-12 col-sm-6">
            <input type="text" class="form-control border-0" name="court" placeholder="Court" style="height: 55px;" required>
        </div>
        <div class="col-12">
            <textarea class="form-control border-0" rows="5" name="qualification" placeholder="Describe your Qualification" required></textarea>
        </div>
        <div class="col-12">
            <textarea class="form-control border-0" rows="5" name="chamber_address" placeholder="Chamber Address" required></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary w-100 py-3" name="save" type="submit">Submit</button>
        </div>
    </div>
</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Appointment End -->
        

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
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

