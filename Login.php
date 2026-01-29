<?php
// Include security configuration FIRST
include("security_config.php");

// Now configure secure session (before session_start())
SecurityConfig::configureSecureSession();

// Disable dangerous PHP functions (runtime level)
@ini_set('disable_functions', 'exec,passthru,shell_exec,system,proc_open,popen,show_source');

// Set security headers
SecurityConfig::setSecurityHeaders();

// Set secure session cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);

session_set_cookie_params([
    'lifetime' => 1800, //new add 1hour
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

//new add
ini_set('session.gc_maxlifetime', 1800); // 1 hour
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);

session_start();
session_regenerate_id(true);

// Set security headers
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

// Include security configuration

$securityLogger = new SecurityLogger($conn);

// ------------------------------
// ✅ CSRF TOKEN PROTECTION ADDED
// ------------------------------
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize login attempts tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_failed_attempt'] = 0;
}

// Check if user is temporarily locked out
if ($_SESSION['login_attempts'] >= 3) {
    $lockout_time = 30; // 30 seconds lockout
    $time_since_last_attempt = time() - $_SESSION['last_failed_attempt'];
    
    if ($time_since_last_attempt < $lockout_time) {
        $remaining_time = $lockout_time - $time_since_last_attempt;
        $error = "Too many failed attempts. Please try again in $remaining_time seconds.";
        $securityLogger->logSuspiciousActivity("Login attempt while locked out");
        header("Location: Login.php?error=" . urlencode($error));
        exit();
    } else {
        // Reset attempts after lockout period
        $_SESSION['login_attempts'] = 0;
    }
}

// --- AJAX endpoint: refresh captcha ---
if (isset($_GET['action']) && $_GET['action'] === 'refresh_captcha') {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $captcha = '';
    try {
        for ($i = 0; $i < 4; $i++) {
            $captcha .= $chars[random_int(0, strlen($chars) - 1)];
        }
    } catch (Exception $e) {
        for ($i = 0; $i < 4; $i++) {
            $captcha .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
    }
    $_SESSION['captcha_code'] = $captcha;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['captcha' => $captcha]);
    exit;
}

// Ensure there's a captcha value in session for the initial page load
if (empty($_SESSION['captcha_code'])) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $captcha = '';
    try {
        for ($i = 0; $i < 4; $i++) {
            $captcha .= $chars[random_int(0, strlen($chars) - 1)];
        }
    } catch (Exception $e) {
        for ($i = 0; $i < 4; $i++) {
            $captcha .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
    }
    $_SESSION['captcha_code'] = $captcha;
}

$error = "";

// Handle login
if (isset($_POST['Login'])) {

    // ✅ Verify CSRF token before any processing
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request (CSRF verification failed).";
        $securityLogger->logSuspiciousActivity("CSRF token validation failed for login attempt");
        echo "<script>window.location.href = 'Login.php?error=" . urlencode($error) . "';</script>";
        exit();
    }

    $email   = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';

    // Sanitize inputs
    $email = SecurityConfig::sanitizeInput($email);
    $captcha = SecurityConfig::sanitizeInput($captcha);
    
    // Check for SQL injection and XSS
    if (SecurityConfig::detectSQLInjection($email) || SecurityConfig::detectXSS($email)) {
        $error = "Invalid input detected";
        $securityLogger->logSuspiciousActivity("Malicious input detected in login attempt for email: $email");
        $_SESSION['login_attempts']++;
        $_SESSION['last_failed_attempt'] = time();
        echo "<script>window.location.href = 'Login.php?error=" . urlencode($error) . "';</script>";
        exit();
    }

    // Validate email format
    if (!SecurityConfig::validateEmail($email)) {
        $error = "Invalid email format";
        $securityLogger->logLoginAttempt($email, false, "Invalid email format");
        $_SESSION['login_attempts']++;
        $_SESSION['last_failed_attempt'] = time();
        echo "<script>window.location.href = 'Login.php?error=" . urlencode($error) . "';</script>";
        exit();
    }

    // Check if user is locked out
    if ($_SESSION['login_attempts'] >= 3) {
        $lockout_time = 1800; // 30 min lockout
        $time_since_last_attempt = time() - $_SESSION['last_failed_attempt'];
        
        if ($time_since_last_attempt < $lockout_time) {
            $remaining_time = $lockout_time - $time_since_last_attempt;
            $error = "Too many failed attempts. Please try again in $remaining_time seconds.";
            $securityLogger->logSuspiciousActivity("Login attempt while locked out for email: $email");
            echo "<script>window.location.href = 'Login.php?error=" . urlencode($error) . "';</script>";
            exit();
        } else {
            $_SESSION['login_attempts'] = 0;
        }
    }

    if (empty($email) || empty($password) || empty($captcha)) {
        $error = "All fields are required";
        $securityLogger->logLoginAttempt($email, false, "Missing required fields");
        $_SESSION['login_attempts']++;
        $_SESSION['last_failed_attempt'] = time();
    } elseif (!isset($_SESSION['captcha_code']) || strtolower($captcha) !== strtolower($_SESSION['captcha_code'])) {
        $error = "Invalid CAPTCHA";
        $securityLogger->logLoginAttempt($email, false, "Invalid CAPTCHA");
        $_SESSION['login_attempts']++;
        $_SESSION['last_failed_attempt'] = time();
    } else {
        // Check for admin credentials first
        if ($email === 'admin@gmail.com' && $password === 'Admin#123') {
            $_SESSION['login_attempts'] = 0;
            session_regenerate_id(true);
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_fullname'] = 'Administrator';
            unset($_SESSION['captcha_code']);
            
            // Log successful admin login
            $securityLogger->logLoginAttempt($email, true);
            
            header("Location: Admin.php");
            exit();
        }
        
        // Prepare statement for regular users
        if ($stmt = $conn->prepare("SELECT * FROM user WHERE email = ?")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Check if password is hashed or plain text (for backward compatibility)
                $password_valid = false;
                if (isset($user['password'])) {
                    if (password_verify($password, $user['password'])) {
                        $password_valid = true;
                    } elseif ($password === $user['password']) {
                        $password_valid = true;
                    }
                }

                if ($password_valid) {
                    $_SESSION['login_attempts'] = 0;
                    session_regenerate_id(true);
                    //new
                    $_SESSION['session_start_time'] = time();
                    $_SESSION['last_activity'] = time();


                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_fullname'] = $user['fullname'] ?? 'User';
                    unset($_SESSION['captcha_code']);
                    
                    // Log successful user login
                    $securityLogger->logLoginAttempt($email, true);
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header("Location: Admin.php");
                    } else {
                        header("Location: HomePage.php");
                    }
                    exit();
                } else {
                    $error = "Invalid email or password";
                    $securityLogger->logLoginAttempt($email, false, "Invalid password");
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_failed_attempt'] = time();
                }
            } else {
                $error = "Invalid email or password";
                $securityLogger->logLoginAttempt($email, false, "Email not found");
                $_SESSION['login_attempts']++;
                $_SESSION['last_failed_attempt'] = time();
            }
            $stmt->close();
        } else {
            $error = "Database error (prepare failed)";
            $securityLogger->logLoginAttempt($email, false, "Database prepare failed");
            $_SESSION['login_attempts']++;
            $_SESSION['last_failed_attempt'] = time();
        }
    }

    if (!empty($error)) {
        // regenerate captcha to avoid repeated attempts
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        $captcha = '';
        try {
            for ($i = 0; $i < 4; $i++) {
                $captcha .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } catch (Exception $e) {
            for ($i = 0; $i < 4; $i++) {
                $captcha .= $chars[mt_rand(0, strlen($chars) - 1)];
            }
        }
        $_SESSION['captcha_code'] = $captcha;

        echo "<script>window.location.href = 'Login.php?error=" . urlencode($error) . "';</script>";
        exit();
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alliance Login System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="wrapper">
        <h1>Login</h1>
        <div class="error-message" id="errorMessage" style="<?php echo isset($_GET['error']) ? 'display:block;' : 'display:none;'; ?>">
            <i class="fas fa-exclamation-circle error-icon"></i>
            <span id="errorText"><?php echo isset($_GET['error']) ? htmlspecialchars($_GET['error']) : ''; ?></span>
        </div>

        <form action="" method="POST" id="loginForm">
            <!-- ✅ Hidden CSRF token added -->
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <i class="fas fa-envelope"></i>
            </div>
            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-lock"></i>
                <span class="password-toggle" id="passwordToggle"><i class="fas fa-eye"></i></span>
            </div>
            <div class="password-hint" id="passwordHint">Must be at least 8 characters with numbers and symbols</div>

            <div class="captcha-container">
                <div class="captcha-code" id="captchaCode"><?php echo htmlspecialchars($_SESSION['captcha_code'] ?? 'AB12'); ?></div>
                <input type="text" class="captcha-input" id="captchaInput" name="captcha" placeholder="Enter CAPTCHA" required>
                <i class="fas fa-sync-alt refresh-captcha" id="refreshCaptcha"></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <a href="forgetPass.php">Forgot Password?</a>
            </div>

            <button type="submit" class="btn" name="Login" id="loginBtn"><i class="fas fa-lock"></i> Login</button>
            <div class="register-link"><p>Don't have an account? <a href="Client_Register.php">Register</a></p></div>
        </form>
    </div>

    <script>
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const passwordHint = document.getElementById('passwordHint');

        passwordToggle.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                passwordToggle.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });

        passwordInput.addEventListener('focus', () => passwordHint.style.display = 'block');
        passwordInput.addEventListener('blur', () => passwordHint.style.display = 'none');

        const captchaCode = document.getElementById('captchaCode');
        const refreshCaptcha = document.getElementById('refreshCaptcha');

        // Request a new captcha from server and update the displayed code
        function generateCaptcha() {
            fetch('Login.php?action=refresh_captcha', { cache: 'no-store' })
                .then(response => response.json())
                .then(data => {
                    if (data && data.captcha) {
                        captchaCode.textContent = data.captcha;
                    } else {
                        // fallback client-side generation if server fails
                        clientSideCaptcha();
                    }
                })
                .catch(() => {
                    clientSideCaptcha();
                });
        }

        function clientSideCaptcha() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            let captcha = '';
            for (let i = 0; i < 4; i++) {
                captcha += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            captchaCode.textContent = captcha;
        }

        refreshCaptcha.addEventListener('click', generateCaptcha);

        const loginForm = document.getElementById('loginForm');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const loginBtn = document.getElementById('loginBtn');

        // Check if user is locked out on page load
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error') && urlParams.get('error').includes('Too many failed attempts')) {
            // Disable the form if user is locked out
            disableForm();
            
            // Extract remaining time from error message
            const errorMsg = urlParams.get('error');
            const timeMatch = errorMsg.match(/(\d+) seconds/);
            if (timeMatch) {
                const remainingTime = parseInt(timeMatch[1]);
                startCountdown(remainingTime);
            }
        }

        function disableForm() {
            document.getElementById('email').disabled = true;
            document.getElementById('password').disabled = true;
            document.getElementById('captchaInput').disabled = true;
            document.getElementById('refreshCaptcha').style.pointerEvents = 'none';
            loginBtn.disabled = true;
            loginBtn.style.opacity = '0.6';
            loginBtn.innerHTML = '<i class="fas fa-lock"></i> Please wait...';
        }

        function enableForm() {
            document.getElementById('email').disabled = false;
            document.getElementById('password').disabled = false;
            document.getElementById('captchaInput').disabled = false;
            document.getElementById('refreshCaptcha').style.pointerEvents = 'auto';
            loginBtn.disabled = false;
            loginBtn.style.opacity = '1';
            loginBtn.innerHTML = '<i class="fas fa-lock"></i> Login';
        }

        function startCountdown(seconds) {
            let remaining = seconds;
            const countdownInterval = setInterval(() => {
                errorText.textContent = `Too many failed attempts. Please try again in ${remaining} seconds.`;
                errorMessage.style.display = 'block';
                
                if (remaining <= 0) {
                    clearInterval(countdownInterval);
                    enableForm();
                    errorMessage.style.display = 'none';
                    // Refresh the page to reset everything
                    window.location.href = 'Login.php';
                }
                
                remaining--;
            }, 1000);
        }

        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // fixed regex

            if (!emailRegex.test(email.value)) {
                email.classList.add('input-error');
                showError('Please enter a valid email address');
                isValid = false;
            } else {
                email.classList.remove('input-error');
            }

            const password = document.getElementById('password');
            if (password.value.length < 8) {
                password.classList.add('input-error');
                showError('Password must be at least 8 characters long');
                isValid = false;
            } else {
                password.classList.remove('input-error');
            }

            const captchaInput = document.getElementById('captchaInput');
            if (captchaInput.value.trim().toLowerCase() !== captchaCode.textContent.trim().toLowerCase()) {
                captchaInput.classList.add('input-error');
                showError('Invalid CAPTCHA code');
                isValid = false;
                // refresh the server-side captcha so client & server match next time
                generateCaptcha();
                captchaInput.value = '';
            } else {
                captchaInput.classList.remove('input-error');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        function showError(message) {
            errorText.textContent = message;
            errorMessage.style.display = 'block';
            setTimeout(() => { errorMessage.style.display = 'none'; }, 5000);
        }

        // On first load, ensure the displayed captcha matches the server session one:
        // Prefer fetching a fresh server captcha to avoid mismatch after redirects.
        generateCaptcha();

        if (urlParams.has('error')) {
            showError(urlParams.get('error'));
        }
    </script>
</body>
</html>