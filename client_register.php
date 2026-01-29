<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Include database connection
include("db.php");

// Initialize variables
$error = '';
$success = '';
$fullname = $phone = $email = '';

if (isset($_POST['cl_button'])) {
    // Collect values
    $fullname = trim($_POST['cl_fullname'] ?? '');
    $email    = strtolower(trim($_POST['cl_email'] ?? ''));
    $raw_pass = $_POST['cl_password'] ?? '';
    $phone    = trim($_POST['cl_phone_number'] ?? '');

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($raw_pass) < 8 || !preg_match("/[A-Z]/", $raw_pass) || 
              !preg_match("/[0-9]/", $raw_pass) || !preg_match("/[^A-Za-z0-9]/", $raw_pass)) {
        $error = "Password must be at least 8 characters with uppercase, number, and special character";
    } else {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT email FROM user WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "User with this email already exists";
            $stmt->close();
        } else {
            $stmt->close();

            // Hash securely
            $hashed_password = password_hash($raw_pass, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO user (fullname, email, password, phone, profilepic, status) 
                                    VALUES (?, ?, ?, ?, 'default_user.png', 'user')");
            $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $phone);

            if ($stmt->execute()) {
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role']  = 'user';
                //$_SESSION['user_id']    = $stmt->insert_id;

                header("Location: HomePage.php");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alliance Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="client_register.css">
</head>
<body>
    <div class="wrapper">
        <h1>Register As Client</h1>
        
        <?php if (!empty($error)): ?>
        <div class="error-message" id="errorMessage">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <span id="errorText"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle success-icon"></i>
            <span id="successText"><?php echo htmlspecialchars($success); ?></span>
        </div>
        <?php endif; ?>
        
        <form action="#" method="POST" id="registrationForm">
            <div class="input-box">
                <input name="cl_fullname" id="fullname" type="text" placeholder="Full Name" required value="<?php echo htmlspecialchars($fullname); ?>">
                <i class="fas fa-user"></i>
            </div>

            <div class="input-box">
                <input name="cl_phone_number" id="phone" type="tel" placeholder="Phone Number (+88)" required value="<?php echo htmlspecialchars($phone); ?>">
                <i class="fas fa-phone"></i>
            </div>
            
            <div class="input-box">
                <input name="cl_email" id="email" type="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="input-box">
                <input name="cl_password" id="password" type="password" placeholder="Password" required>
                <span class="password-toggle" id="passwordToggle">
                    <i class="fas fa-eye"></i>
                </span>
                <i class="fas fa-lock"></i>
            </div>
            
            <div class="password-strength" id="passwordStrength">
                <div class="strength-section" id="strength1"></div>
                <div class="strength-section" id="strength2"></div>
                <div class="strength-section" id="strength3"></div>
                <div class="strength-section" id="strength4"></div>
            </div>
            
            <div class="password-requirements">
                <p class="requirement" id="lengthReq"><i class="fas fa-check-circle valid" id="lengthIcon"></i> At least 8 characters</p>
                <p class="requirement" id="numberReq"><i class="fas fa-check-circle valid" id="numberIcon"></i> Contains a number</p>
                <p class="requirement" id="upperReq"><i class="fas fa-check-circle valid" id="upperIcon"></i> Contains uppercase letter</p>
                <p class="requirement" id="specialReq"><i class="fas fa-check-circle valid" id="specialIcon"></i> Contains special character</p>
            </div>
            
            <div class="terms">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>
            
            <button name="cl_button" type="submit" class="btn">Register Securely</button>
            
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script>
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthBars = [
            document.getElementById('strength1'),
            document.getElementById('strength2'),
            document.getElementById('strength3'),
            document.getElementById('strength4')
        ];
        
        const lengthIcon = document.getElementById('lengthIcon');
        const numberIcon = document.getElementById('numberIcon');
        const upperIcon = document.getElementById('upperIcon');
        const specialIcon = document.getElementById('specialIcon');
        
        const lengthReq = document.getElementById('lengthReq');
        const numberReq = document.getElementById('numberReq');
        const upperReq = document.getElementById('upperReq');
        const specialReq = document.getElementById('specialReq');
        
        const passwordToggle = document.getElementById('passwordToggle');
        
        passwordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            let strength = 0;           
            if (password.length >= 8) {
                strength++;
                lengthIcon.className = 'fas fa-check-circle valid';
                lengthReq.style.color = '#4CAF50';
            } else {
                lengthIcon.className = 'fas fa-times-circle invalid';
                lengthReq.style.color = '#f44336';
            }           
            if (/\d/.test(password)) {
                strength++;
                numberIcon.className = 'fas fa-check-circle valid';
                numberReq.style.color = '#4CAF50';
            } else {
                numberIcon.className = 'fas fa-times-circle invalid';
                numberReq.style.color = '#f44336';
            }  
            if (/[A-Z]/.test(password)) {
                strength++;
                upperIcon.className = 'fas fa-check-circle valid';
                upperReq.style.color = '#4CAF50';
            } else {
                upperIcon.className = 'fas fa-times-circle invalid';
                upperReq.style.color = '#f44336';
            }    
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                strength++;
                specialIcon.className = 'fas fa-check-circle valid';
                specialReq.style.color = '#4CAF50';
            } else {
                specialIcon.className = 'fas fa-times-circle invalid';
                specialReq.style.color = '#f44336';
            }  
            strengthBars.forEach((bar, index) => {
                if (index < strength) {
                    switch(strength) {
                        case 1: bar.style.background = '#f44336'; break;
                        case 2: bar.style.background = '#ff9800'; break;
                        case 3: bar.style.background = '#ffeb3b'; break;
                        case 4: bar.style.background = '#4CAF50'; break;
                    }
                } else {
                    bar.style.background = '#ddd';
                }
            });
        });
        
        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
        
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            let isValid = true;
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            
            const fullname = document.getElementById('fullname').value;
            if (!/^[a-zA-Z\s]{2,50}$/.test(fullname)) {
                isValid = false;
                errorText.textContent = 'Please enter a valid full name (2-50 letters and spaces only)';
                errorMessage.style.display = 'flex';
            }
            
            const email = document.getElementById('email').value;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                isValid = false;
                errorText.textContent = 'Please enter a valid email address';
                errorMessage.style.display = 'flex';
            }
            
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                isValid = false;
                errorText.textContent = 'Password must be at least 8 characters long';
                errorMessage.style.display = 'flex';
            }
            
            const phone = document.getElementById('phone').value;
            if (!/^[\+]?[1-9][\d]{0,15}$/.test(phone.replace(/\s/g, ''))) {
                isValid = false;
                errorText.textContent = 'Please enter a valid phone number';
                errorMessage.style.display = 'flex';
            }
            
            const terms = document.getElementById('terms').checked;
            if (!terms) {
                isValid = false;
                errorText.textContent = 'You must agree to the terms and conditions';
                errorMessage.style.display = 'flex';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        document.getElementById('fullname').addEventListener('blur', function() {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '').trim();
        });
        
        document.getElementById('email').addEventListener('blur', function() {
            this.value = this.value.toLowerCase().trim();
        });
        
        document.getElementById('phone').addEventListener('blur', function() {
            this.value = this.value.replace(/[^\d\+]/g, '').trim();
        });
    </script>
</body>
</html>
