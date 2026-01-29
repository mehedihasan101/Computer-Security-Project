<?php
//session_start();
include("db.php");

// Initialize variables
$userprofile = null;
$isLawyer = false;

// Check if session variable is set
if (isset($_SESSION['useremail'])) {
    $userprofile = $_SESSION['useremail'];

    // Attempt to query the user table
    $sql = "SELECT * FROM user WHERE email='$userprofile'";
    $result = $conn->query($sql);

    // Query to check if the user is a lawyer
    $sql2 = "SELECT l.lawyer_id as lawyer_id
          FROM user AS u 
          JOIN lawyer AS l ON u.email = l.email
          WHERE u.status = 'lawyer' AND u.email='$userprofile'";
    $result2 = $conn->query($sql2);

    // Check if the user is a lawyer
    if ($result2 && $result2->num_rows > 0) {
        $isLawyer = true; // Set the flag to true if user is a lawyer
    }

    // Check if the user query executed successfully
    if ($result && $result->num_rows > 0) {
        // Fetch user data
        $data = mysqli_fetch_assoc($result);
    } else {
        echo "No user data found.";
    }
} else {
    // If session variable is not set
    echo "User email not set in session.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
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

            <!-- Display either 'Register as Lawyer' or 'Client Request' based on user status -->
            <?php if ($isLawyer): ?>
                <a href="client_requests.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Client Request<i class="fa fa-arrow-right ms-3"></i></a>
            <?php else: ?>
                <a href="lawyer_registration.php" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block">Register<i class="fa fa-arrow-right ms-3"></i><br>as lawyer</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Add your page content below -->
    
    <!-- Bootstrap JS and dependencies (if needed) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
