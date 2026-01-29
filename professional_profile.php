<?php
session_start();
include("db.php");

// Security: check if lawyer is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// CSRF protection: generate token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the lawyer ID from the URL (only allow numbers)
$Lid = isset($_GET['lawyer_id']) ? intval($_GET['lawyer_id']) : null;

$free = $fee = $catagory = $qualification = $court = $error = "";

// Ensure a valid lawyer ID is provided
if ($Lid) {
    // Fetch lawyer details
    $sql = $conn->prepare("SELECT * FROM lawyer WHERE lawyer_id = ?");
    $sql->bind_param("i", $Lid);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $free = $data["free_time"];
        $fee = $data["fee"];
        $catagory = $data["catagory"];
        $qualification = $data["qualification"];
        $court = $data["court"];

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
            // Verify CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $error = "Invalid request. Please try again.";
            } else {
                // Retrieve and sanitize inputs
                $free = trim($_POST["free"] ?? $free);
                $fee = trim($_POST["fee"] ?? $fee);
                $catagory = trim($_POST["catagory"] ?? $catagory);
                $qualification = trim($_POST["qualification"] ?? $qualification);
                $court = trim($_POST["court"] ?? $court);

                // Update lawyer details securely
                $sql2 = $conn->prepare("UPDATE lawyer 
                                        SET free_time=?, fee=?, qualification=?, catagory=?, court=? 
                                        WHERE lawyer_id=?");
                $sql2->bind_param("sssssi", $free, $fee, $qualification, $catagory, $court, $Lid);

                if ($sql2->execute()) {
                    unset($_SESSION['csrf_token']); // regenerate for safety
                    header("Location: profile.php?msg=Data updated successfully");
                    exit();
                } else {
                    $error = "There was an issue updating the profile. Please try again.";
                }
            }
        }
    } else {
        $error = "No lawyer data found for the provided ID.";
    }
} else {
    $error = "Invalid or missing lawyer ID.";
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Professional Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="homepage.php">Alliance</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="homepage.php">Home</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="col-lg-6 m-auto">
        <form action="" method="post">
            <div class="card">
                <div class="card-header bg-warning">
                    <h2 class="text-white text-center">Update Professional Profile</h2>
                </div>
                <div class="card-body">
                    <?php if ($error) { echo "<div class='alert alert-danger'>".htmlspecialchars($error)."</div>"; } ?>
                    <div class="form-group">
                        <label>Qualification:</label>
                        <input type="text" name="qualification" value="<?php echo htmlspecialchars($qualification); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Court:</label>
                        <input type="text" name="court" value="<?php echo htmlspecialchars($court); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Category:</label>
                        <input type="text" name="catagory" value="<?php echo htmlspecialchars($catagory); ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Available Time:</label>
                        <textarea class="form-control border-1" rows="5" name="free" required><?php echo htmlspecialchars($free); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Fee:</label>
                        <input type="text" name="fee" value="<?php echo htmlspecialchars($fee); ?>" class="form-control" required>
                    </div>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                </div>
                <div class="card-footer text-center">
                    <button type="submit" name="submit" class="btn btn-success">Submit</button>
                    <a href="profile.php" class="btn btn-info">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>
