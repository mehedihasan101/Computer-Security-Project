<?php
// appointment.php (fixed + secure)
// === Backend (do NOT move below HTML) ===

session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// DB connection
include("db.php");

// --- Resolve session email robustly (handles different session key names) ---
$possibleKeys = ['useremail', 'user_email', 'userEmail', 'user_email_address', 'email'];
$sessionEmail = null;
foreach ($possibleKeys as $k) {
    if (!empty($_SESSION[$k])) {
        $sessionEmail = $_SESSION[$k];
        // normalize primary session key for other pages
        $_SESSION['useremail'] = $sessionEmail;
        break;
    }
}

// If no session email, redirect to login (preserve return URL)
if (empty($sessionEmail)) {
    // Build full requested URL to return after login
    $currentUrl = $_SERVER['REQUEST_URI'];
    header("Location: login.php?redirect=" . urlencode($currentUrl));
    exit();
}

// Fetch logged-in user securely
$stmt1 = $conn->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
$stmt1->bind_param("s", $sessionEmail);
$stmt1->execute();
$result1 = $stmt1->get_result();

if (!$result1 || $result1->num_rows === 0) {
    // Session exists but user not found in DB -> force re-login
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$data = $result1->fetch_assoc();
$stmt1->close();

// CSRF token for appointment form
if (empty($_SESSION['csrf_token_appointment'])) {
    $_SESSION['csrf_token_appointment'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token_appointment'];

// Initialize variables for form validation & display
$appointmentdateErr = $appointmenttimeErr = $reasonErr = $phoneErr = $opphoneErr = "";
$appointmentdate = $appointmenttime = $reason = $num1 = $num2 = "";
$lawyer_id = "";
$datax = null;

// --- Get lawyer_id from GET and fetch lawyer details (prepared) ---
if (isset($_GET['lawyer_id']) && $_GET['lawyer_id'] !== "") {
    $lawyer_id = $_GET['lawyer_id'];

    // Use string binding — lawyer_id may be alphanumeric in some schemas
    $q = "SELECT u.email AS email, l.fee, l.free_time, l.date, l.lawyer_id, l.full_name AS full_name, 
                 l.catagory AS specialization, l.court AS court, l.qualification, u.profilepic AS profilepic
          FROM user AS u
          JOIN lawyer AS l ON u.email = l.email
          WHERE u.status = 'lawyer' AND l.lawyer_id = ? LIMIT 1";
    $stmtx = $conn->prepare($q);
    $stmtx->bind_param("s", $lawyer_id);
    $stmtx->execute();
    $rx = $stmtx->get_result();
    $datax = $rx->fetch_assoc();
    $stmtx->close();

    if (!$datax) {
        // Invalid lawyer_id — redirect back to lawyer list
        echo "<script>alert('Selected lawyer not found.'); window.location.href='lawyer.php';</script>";
        exit();
    }
} else {
    // No lawyer selected -> redirect back
    header("Location: lawyer.php");
    exit();
}

// --- Handle POST submission securely ---
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    // Validate CSRF
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token_appointment'], $_POST['csrf_token'])) {
        echo "<script>alert('Invalid form submission (CSRF). Please refresh and try again.'); window.location.href='" . htmlspecialchars($_SERVER['REQUEST_URI']) . "';</script>";
        exit();
    }

    $valid = true;
    $today = date("Y-m-d");

    // appointment date
    if (empty($_POST['appointmentdate'])) {
        $appointmentdateErr = "Appointment date is required.";
        $valid = false;
    } else {
        $appointmentdate = $_POST['appointmentdate'];
        // ISO date compare (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentdate)) {
            $appointmentdateErr = "Invalid date format.";
            $valid = false;
        } elseif ($appointmentdate < $today) {
            $appointmentdateErr = "You cannot select a past date for the appointment.";
            $valid = false;
        }
    }

    // appointment time
    if (empty($_POST['appointmenttime'])) {
        $appointmenttimeErr = "Appointment time is required.";
        $valid = false;
    } else {
        $appointmenttime = $_POST['appointmenttime'];
        // basic hh:mm validation
        if (!preg_match('/^[0-2]\d:[0-5]\d$/', $appointmenttime)) {
            $appointmenttimeErr = "Invalid time format.";
            $valid = false;
        }
    }

    // reason
    if (empty(trim($_POST['reason'] ?? ''))) {
        $reasonErr = "Reason is required.";
        $valid = false;
    } else {
        $reason = trim($_POST['reason']);
        // limit length
        if (mb_strlen($reason) > 1000) {
            $reasonErr = "Reason is too long.";
            $valid = false;
        }
    }

    // phone
    if (empty(trim($_POST['phone'] ?? ''))) {
        $phoneErr = "Phone number is required.";
        $valid = false;
    } else {
        $num1 = trim($_POST['phone']);
        if (!preg_match("/^[0-9+\-\s()]{6,20}$/", $num1)) {
            $phoneErr = "Invalid phone format.";
            $valid = false;
        }
    }

    // guardian phone
    if (empty(trim($_POST['opphone'] ?? ''))) {
        $opphoneErr = "Guardian's phone number is required.";
        $valid = false;
    } else {
        $num2 = trim($_POST['opphone']);
        if (!preg_match("/^[0-9+\-\s()]{6,20}$/", $num2)) {
            $opphoneErr = "Invalid phone format.";
            $valid = false;
        }
    }

    if ($valid) {
        $email = $data['email']; // logged-in user's email

        // Check duplicate booking (prepared)
        $checkQuery = "SELECT 1 FROM appointment WHERE lawyer_id = ? AND appointment_date = ? AND appointment_time = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bind_param("sss", $lawyer_id, $appointmentdate, $appointmenttime);
        $stmtCheck->execute();
        $checkRes = $stmtCheck->get_result();

        if ($checkRes && $checkRes->num_rows > 0) {
            echo "<script>alert('Lawyer is already booked for your given time. Please change date/time.');</script>";
        } else {
            // Insert appointment securely
            $insertQuery = "INSERT INTO appointment (user_email, lawyer_id, appointment_date, appointment_time, status, reason, num, opnum)
                            VALUES (?, ?, ?, ?, 'pending', ?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param("sssssss", $email, $lawyer_id, $appointmentdate, $appointmenttime, $reason, $num1, $num2);

            if ($stmtInsert->execute()) {
                // regenerate CSRF token to avoid replay
                $_SESSION['csrf_token_appointment'] = bin2hex(random_bytes(32));
                echo "<script>alert('Your request has been sent to the lawyer. Please wait for the lawyer response.');</script>";
                // redirect back to appointment page for the same lawyer (to show result)
                echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF']) . "?lawyer_id=" . urlencode($lawyer_id) . "';</script>";
                exit();
            } else {
                echo "<script>alert('Database error. Please try again later.');</script>";
            }
            $stmtInsert->close();
        }
        $stmtCheck->close();
    }
}

// close DB connection at the end of execution (HTML will be output below)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Alliance</title>
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">Book Appointment</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Book Appointment</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->
    
    
    <!-- Appointment Form Section -->
    <div class="container-xxl py-5">
    <div class="row g-5">
                <!-- Left side: Lawyer information -->
                    <div >
                    <h1 >Why you hire lawyers from us ?</h1>
                    <p >Alliance Consultancy Firm is a well respected law firm in Bangladesh. 
                        We provide highly qualified lawyers who have been practicing in the supreme court of Bangladesh for long time.
                         They have vast knowledge in their practicing area. Moreover, our lawyers success rate is over 85%</p>
                    
                </div>
    </div>

        <div class="container">
            <div class="row g-5">
                <!-- Left side: Lawyer information -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="bg-light rounded p-5 d-flex">
                        <div class="me-4">
                            <?php 
                                // safe output
                                $pic = htmlspecialchars($datax['profilepic'] ?? 'img/default.png');
                                echo "<img class='img-fluid rounded' src='".$pic."' width='270' height='150'>"; 
                            ?>
                        </div>
                        <div>
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($datax['full_name'] ?? ''); ?></h5>
                                <p class="card-text"><strong>Lawyer ID:</strong> <?php echo htmlspecialchars($datax['lawyer_id'] ?? ''); ?></p>
                                <p class="card-text"><strong>Specialization:</strong> <?php echo htmlspecialchars($datax['specialization'] ?? ''); ?></p>
                                <p class="card-text"><strong>Court:</strong> <?php echo htmlspecialchars($datax['court'] ?? ''); ?></p>
                                <p class="card-text"><strong>Qualification:</strong> <?php echo htmlspecialchars($datax['qualification'] ?? ''); ?></p>
                                <p class="card-text"><strong>Available Time:</strong> <?php echo htmlspecialchars($datax['free_time'] ?? ''); ?></p>
                                <p class="card-text"><strong>Fees:</strong> <?php echo htmlspecialchars($datax['fee'] ?? ''); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right side: Appointment form -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="bg-light rounded h-100 d-flex align-items-center p-5">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"].'?lawyer_id='.urlencode($lawyer_id)); ?>">
                            <div class="row g-3">
                                <!-- User's Name -->
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" name="fullname" value="<?php echo htmlspecialchars($data['fullname']); ?>" style="height: 55px;" readonly>
                                </div>

                                <!-- User's Email -->
                                <div class="col-12 col-sm-6">
                                    <input type="email" class="form-control border-0" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" style="height: 55px;" readonly>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" name="phone" placeholder="Your Mobile" style="height: 55px;" value="<?php echo htmlspecialchars($num1); ?>">
                                    <span class="error"><?php echo $phoneErr;?></span>
                                </div>

                                <!-- Guardian's Phone Number -->
                                <div class="col-12 col-sm-6">
                                    <input type="text" class="form-control border-0" name="opphone" placeholder="Guardian Mobile" style="height: 55px;" value="<?php echo htmlspecialchars($num2); ?>">
                                    <span class="error"><?php echo $opphoneErr;?></span>
                                </div>

                                <!-- Appointment Date Picker -->
<div class="col-12 col-sm-6">
    <input type="date" name="appointmentdate" class="form-control border-0" style="height: 55px;" value="<?php echo htmlspecialchars($appointmentdate); ?>">
    <span class="error"><?php echo $appointmentdateErr;?></span>
</div>

<!-- Appointment Time Picker -->
<div class="col-12 col-sm-6">
    <input type="time" name="appointmenttime" class="form-control border-0" style="height: 55px;" value="<?php echo htmlspecialchars($appointmenttime); ?>">
    <span class="error"><?php echo $appointmenttimeErr;?></span>
</div>

                                <!-- Reason for Appointment -->
                                <div class="col-12">
                                    <textarea class="form-control border-0" name="reason" placeholder="Reason for Appointment"><?php echo htmlspecialchars($reason); ?></textarea>
                                    <span class="error"><?php echo $reasonErr;?></span>
                                </div>

                                <!-- CSRF token (hidden) -->
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit" name="submit">Book Appointment</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (unchanged) -->
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

    <!-- JS libs (unchanged) -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>

<?php
// Close connection
$conn->close();
?>
