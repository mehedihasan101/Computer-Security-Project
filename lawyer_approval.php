<?php
// --- Security Headers ---
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: no-referrer-when-downgrade");
// lawyer_approval.php
include("db.php");
session_start();

// ====== Session check (compatible with both session names used in different pages) ======
if (isset($_SESSION['user_email'])) {
    $session_email = $_SESSION['user_email'];
} elseif (isset($_SESSION['useremail'])) {
    // compatibility fallback
    $session_email = $_SESSION['useremail'];
} else {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// ====== Determine lawyer ID (prefer GET but verify ownership) ======
$lawyer_id = 0;
if (isset($_GET['lawyer_id']) && intval($_GET['lawyer_id']) > 0) {
    $lawyer_id = intval($_GET['lawyer_id']);

    // Verify that this lawyer_id belongs to the logged-in user
    $stmtCheck = $conn->prepare("SELECT email FROM lawyer WHERE lawyer_id = ? LIMIT 1");
    $stmtCheck->bind_param("i", $lawyer_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();
    if ($resCheck && $resCheck->num_rows > 0) {
        $rowCheck = $resCheck->fetch_assoc();
        if ($rowCheck['email'] !== $session_email) {
            $error = "You are not authorized to view this page for that lawyer.";
            $stmtCheck->close();
        } else {
            $stmtCheck->close();
        }
    } else {
        $error = "Lawyer not found.";
        $stmtCheck->close();
    }
} else {
    // Try to fetch lawyer_id from lawyer table using session email
    $stmtL = $conn->prepare("SELECT lawyer_id FROM lawyer WHERE email = ? LIMIT 1");
    $stmtL->bind_param("s", $session_email);
    $stmtL->execute();
    $resL = $stmtL->get_result();
    if ($resL && $resL->num_rows > 0) {
        $rowL = $resL->fetch_assoc();
        $lawyer_id = intval($rowL['lawyer_id']);
    } else {
        $error = "Lawyer not found for your account.";
    }
    $stmtL->close();
}

// If there's an auth/lookup error, show page with message (no redirects)
if (empty($error)) {

    // ====== Handle POST update (accept/reject) ======
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['appointment_id'])) {
        $action = $_POST['action'];
        $appointment_id = intval($_POST['appointment_id']);
        $ltext = isset($_POST['ltext']) ? trim($_POST['ltext']) : '';
        $link = isset($_POST['link']) ? trim($_POST['link']) : '';

        // Server-side validation
        if ($action === 'accept' && (empty($ltext) || empty($link))) {
            // redirect back with error (use querystring so no output happens before header)
            header("Location: lawyer_approval.php?lawyer_id=" . $lawyer_id . "&err=fillboth");
            exit;
        } elseif ($action === 'reject' && empty($ltext)) {
            header("Location: lawyer_approval.php?lawyer_id=" . $lawyer_id . "&err=fillfeedback");
            exit;
        }

        if ($action === 'accept') {
            $new_status = 'accepted';
            $stmtUpd = $conn->prepare("UPDATE appointment SET status = ?, link = ?, ltext = ? WHERE appointment_id = ? AND lawyer_id = ?");
            $stmtUpd->bind_param("sssii", $new_status, $link, $ltext, $appointment_id, $lawyer_id);
        } else { // reject
            $new_status = 'rejected';
            $stmtUpd = $conn->prepare("UPDATE appointment SET status = ?, ltext = ? WHERE appointment_id = ? AND lawyer_id = ?");
            $stmtUpd->bind_param("ssii", $new_status, $ltext, $appointment_id, $lawyer_id);
        }

        if ($stmtUpd->execute()) {
            $stmtUpd->close();
            header("Location: lawyer_approval.php?lawyer_id=" . $lawyer_id . "&msg=updated");
            exit;
        } else {
            $stmtUpd->close();
            header("Location: lawyer_approval.php?lawyer_id=" . $lawyer_id . "&err=sql");
            exit;
        }
    }

    // ====== Fetch pending appointments for this lawyer ======
    $appointments = [];
    $stmtApp = $conn->prepare("SELECT * FROM appointment WHERE status = 'pending' AND lawyer_id = ? ORDER BY appointment_date DESC, appointment_time DESC");
    $stmtApp->bind_param("i", $lawyer_id);
    $stmtApp->execute();
    $resApp = $stmtApp->get_result();
    while ($row = $resApp->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmtApp->close();
}

// close DB at end
// $conn->close(); // keep open until the end of the script
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Alliance</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700;900&display=swap" rel="stylesheet"> 
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Keep your original styling exactly as provided */
        * { padding:0; margin:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color:#f4f7f6; color:#333; }
        h2 { text-align:center; color:#333; font-size:28px; font-weight:600; margin-bottom:20px; }
        .table-box { width:100%; padding:20px; }
        .table-box table { width:100%; border-collapse:collapse; }
        .table-box table th, .table-box table td { border:1px solid #e0e0e0; padding:15px; font-size:18px; text-align:center; }
        .table-box table th { background-color:#0077cc; color:white; font-size:20px; }
        .table-box table td { background-color:#fafafa; }
        .accept-btn, .reject-btn { padding:10px 15px; margin-right:5px; background-color:#4CAF50; border:none; color:white; font-size:16px; font-weight:bold; border-radius:5px; cursor:pointer; transition:background-color 0.3s ease; }
        .accept-btn:hover { background-color:#45a049; }
        .reject-btn { background-color:#f44336; }
        .reject-btn:hover { background-color:#e03131; }
        input[type="text"], textarea { padding:8px; width:90%; margin-top:10px; border:1px solid #ccc; border-radius:4px; font-size:16px; transition:border-color 0.3s ease; }
        input[type="text"]:focus, textarea:focus { border-color:#0077cc; outline:none; }
        textarea { resize:vertical; height:80px; }
    </style>
</head>
<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width:3rem; height:3rem;" role="status">
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
                <a href="others.php" class="nav-item nav-link ">Expertise</a>
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
            <a href="lawyer_registration.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Register<i class="fa fa-arrow-right ms-3"></i><br>as lawyer</a>
        </div>
    </nav>
    <!-- Navbar End -->

    <style>
    .page-header { background: url("header-page.jpg") top center no-repeat; background-size: cover; text-shadow:0 0 30px rgba(0,0,0,.1); }
    </style>

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white mb-3 animated slideInDown">Booking Request</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">Request list</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container">
        <h2>Request Checklist</h2>

        <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
            <div style="text-align:center; color:green; margin-bottom:10px;">Record updated successfully.</div>
        <?php endif; ?>
        <?php if (!empty($_GET['err'])): ?>
            <div style="text-align:center; color:red; margin-bottom:10px;">
                <?php
                    if ($_GET['err'] === 'fillboth') echo "Please fill both feedback and link before accepting.";
                    elseif ($_GET['err'] === 'fillfeedback') echo "Please fill in the feedback before rejecting.";
                    elseif ($_GET['err'] === 'sql') echo "Database error while updating.";
                    else echo "An error occurred.";
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="text-align:center; color:red; margin-bottom:10px;"><?php echo htmlspecialchars($error); ?></div>
        <?php else: ?>
            <div class="table-box">
                <table border="1">
                    <tr>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Reason</th>
                        <th>Link</th>
                        <th>Feedback</th>
                        <th>Action</th>
                    </tr>

                    <?php if (count($appointments) === 0): ?>
                        <tr>
                            <td colspan="6">No pending appointments.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $row): ?>
                            <tr>
                                <form method="post" action="lawyer_approval.php?lawyer_id=<?php echo $lawyer_id; ?>">
                                    <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['appointment_time']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td>
                                        <input type="text" style="height:40px; width:250px" name="link" placeholder="Meet link" value="">
                                    </td>
                                    <td>
                                        <textarea style="height:150px; width:250px" name="ltext" rows="3" placeholder="Enter feedback here..." required></textarea>
                                    </td>
                                    <td>
                                        <input type="hidden" name="appointment_id" value="<?php echo intval($row['appointment_id']); ?>">
                                        <button type="submit" class="accept-btn" name="action" value="accept">Accept</button>
                                        <button type="submit" class="reject-btn" name="action" value="reject">Reject</button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        <?php endif; ?>
    </div>

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
// close DB connection
$conn->close();
?>
