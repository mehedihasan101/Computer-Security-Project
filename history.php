<?php 
session_start();
include("db.php");

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$userprofile = $_SESSION['user_email'];

// Fetch user data safely
$stmt = $conn->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $userprofile);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

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

<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f3f6fa;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .main-container {
        max-width: 1300px;
        margin: 0 auto;
        padding: 20px;
    }

    .container1 {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        margin-top: 50px;
    }

    h1 {
        text-align: center;
        font-size: 2.5rem;
        color: #333;
        padding: 15px;
        border-radius: 50px;
        margin-bottom: 50px;
        font-weight: bold;
    }

    .arbitration, .mediation, .lawyer {
        margin-top: 40px;
    }

    .arbitration h2, .mediation h2, .lawyer h2 {
        font-size: 1.75rem;
        text-align: center;
        color: #333;
        padding: 10px;
        margin-bottom: 20px;
        position: relative;
    }

    .arbitration h2::after, .mediation h2::after, .lawyer h2::after {
        content: "";
        display: block;
        height: 4px;
        background: linear-gradient(90deg, #ff6b6b, #f06595, #48c6ef, #6f86d6);
        margin: 10px auto;
        width: 100%;
        border-radius: 5px;
        animation: moveGradient 2s linear infinite;
    }

    @keyframes moveGradient {
        0% { background-position: 0 0; }
        100% { background-position: 100% 0; }
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: #f9fbfd;
    }

    th, td {
        padding: 12px 20px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #48c6ef;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    td {
        color: #555;
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px;
        }

        th, td {
            padding: 10px;
        }

        h1 {
            font-size: 2rem;
        }
    }
</style>
</head>
<body>
  <!-- Navbar and other content here -->
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
            <h1 class="display-3 text-white mb-3 animated slideInDown">View History</h1>
            <nav aria-label="breadcrumb animated slideInDown">
                <ol class="breadcrumb text-uppercase mb-0">
                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                    <li class="breadcrumb-item text-primary active" aria-current="page">View History</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Page Header End -->
  
<div class="main-container">
    <div class="container1">
        <h1>View History</h1>
        <hr>
        <div class="arbitration">
            <h2>Arbitration</h2>
            <table>
                <thead>
                    <tr>
                        <th>Plaintiff</th>
                        <th>Defendant</th>
                        <th>Issues</th>
                        <th>Case Number</th>
                        <th>Online Status</th>
                        <th>Assigned Arbitrator ID</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $arbitration_sql = "SELECT * FROM arbitration_proposal 
                                        WHERE ((email1='$userprofile') OR (email2='$userprofile'))";
                    $arbitration_result = $conn->query($arbitration_sql);

                    while ($row = $arbitration_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['person1'] . "</td>";
                        echo "<td>" . $row['person2'] . "</td>";
                        echo "<td>" . $row['issues'] . "</td>";
                        echo "<td>" . $row['casenumber'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['arbitrator_id'] . "</td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>

        <div class="mediation">
            <h2>Mediation</h2>
            <table>
                <thead>
                    <tr>
                        <th>Plaintiff</th>
                        <th>Defendant</th>
                        <th>Issues</th>
                        <th>Case Number</th>
                        <th>Online Status</th>
                        <th>Assigned Mediator ID</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $mediation_sql = "SELECT person1,person2,issues,status,casenumber, mediator_id FROM mediation_proposal 
                                      WHERE (email1='$userprofile' or email2='$userprofile')";
                    $mediation_result = $conn->query($mediation_sql);

                    while ($row = $mediation_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['person1'] . "</td>";
                        echo "<td>" . $row['person2'] . "</td>";
                        echo "<td>" . $row['issues'] . "</td>";
                        echo "<td>" . $row['casenumber'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['mediator_id'] . "</td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>

        <div class="lawyer">
            <h2>Lawyer</h2>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Person</th>
                        <th>Lawyer</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>lawyer feedback</th>
                        <th>Status</th>
                        <th>link</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $a = $data['email'];
                    $lawyer_sql = "SELECT a.appointment_id, l.email, a.user_email, a.appointment_date,
                     a.appointment_time,u.fullname AS Person,l.full_name AS Lawyer,a.ltext as ltext,
                     a.reason as reason, a.link as link, a.status AS status
                                   FROM lawyer AS l
                                   JOIN appointment AS a
                                   ON l.lawyer_id = a.lawyer_id
                                   JOIN user AS u ON a.user_email=u.email
                                   WHERE l.email='$a' OR a.user_email='$a'";
                    $lawyer_result = $conn->query($lawyer_sql);

                    while ($row = $lawyer_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['appointment_id'] . "</td>";
                        echo "<td>" . $row['Person'] . "</td>";
                        echo "<td>" . $row['Lawyer'] . "</td>";
                        echo "<td>" . $row['reason'] . "</td>";
                        echo "<td>" . $row['appointment_date'] . "</td>";
                        echo "<td>" . $row['appointment_time'] . "</td>";
                        echo "<td>" . $row['ltext'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['link'] . "</td>";
                        echo "</tr>";
                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Profile Section End -->

    <!-- Footer Section -->
    <!-- Footer code remains unchanged -->

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
<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer pt-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-light mb-4">Address</h5>
                    <p><i class="fa fa-map-marker-alt me-3"></i>Dhaka</p>
                    <p><i class="fa fa-phone-alt me-3"></i>+01780337775</p>
                    <p><i class="fa fa-envelope me-3"></i>alliance247@gmail.com</p>
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
