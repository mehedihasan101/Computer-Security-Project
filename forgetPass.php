<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Database connection
$host = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'ADR';

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";
$verified = false;

// STEP 1: Check user info (Fullname, Email, Phone)
if (isset($_POST['verifyUser'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($fullname) || empty($email) || empty($phone)) {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM user WHERE fullname = ? AND email = ? AND phone = ?");
        if ($stmt) {
            $stmt->bind_param("sss", $fullname, $email, $phone);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['verified'] = true;
                $verified = true;
                $message = "Verification successful! Now set your new password.";
            } else {
                $message = "Invalid details. Please check and try again.";
            }
            $stmt->close();
        }
    }
}

// STEP 2: Change password after verification
if (isset($_POST['resetPassword'])) {
    if (isset($_SESSION['verified']) && $_SESSION['verified'] === true) {
        $email = $_SESSION['reset_email'];
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $message = "Please fill in both password fields.";
            $verified = true;
        } elseif ($newPassword !== $confirmPassword) {
            $message = "Passwords do not match.";
            $verified = true;
        } else {
            $updateStmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
            if ($updateStmt) {
                $updateStmt->bind_param("ss", $newPassword, $email);
                if ($updateStmt->execute()) {
                    session_destroy();
                    header("Location: login.php?success=Password updated successfully!");
                    exit();
                } else {
                    $message = "Error updating password: " . $updateStmt->error;
                }
                $updateStmt->close();
            }
        }
    } else {
        $message = "Please verify your details first.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="wrapper">
        <h1>Reset Password</h1>
        <div class="message" style="<?php echo !empty($message) ? 'display:block;' : 'display:none;'; ?>">
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>

        <form action="" method="POST" id="resetForm">
            <?php if (!$verified && empty($_SESSION['verified'])): ?>
                <!-- Step 1: Verify user info -->
                <p>Please provide your personal information to verify your identity.</p>
                <div class="input-box">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="input-box">
                    <input type="text" name="phone" placeholder="Phone Number" required>
                    <i class="fas fa-phone"></i>
                </div>
                <button type="submit" class="btn" name="verifyUser">Verify</button>

            <?php else: ?>
                <!-- Step 2: Change password -->
                <p>Enter your new password below.</p>
                <div class="input-box">
                    <input type="password" id="newPassword" name="newPassword" placeholder="New Password" required>
                    <i class="fas fa-lock"></i>
                    <span class="password-toggle" id="newPasswordToggle"><i class="fas fa-eye"></i></span>
                </div>
                <div class="input-box">
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm New Password" required>
                    <i class="fas fa-lock"></i>
                    <span class="password-toggle" id="confirmPasswordToggle"><i class="fas fa-eye"></i></span>
                </div>
                <button type="submit" class="btn" name="resetPassword">Reset Password</button>
            <?php endif; ?>

            <div class="register-link">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </form>
    </div>

    <script>
        function setupPasswordToggle(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            if (input && toggle) {
                toggle.addEventListener("click", function() {
                    if (input.type === "password") {
                        input.type = "text";
                        this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    } else {
                        input.type = "password";
                        this.innerHTML = '<i class="fas fa-eye"></i>';
                    }
                });
            }
        }
        setupPasswordToggle("newPassword", "newPasswordToggle");
        setupPasswordToggle("confirmPassword", "confirmPasswordToggle");
    </script>
</body>
</html>
