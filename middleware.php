
<?php

// // middleware.php - Access control middleware (email is primary key)

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// // include DB connection (adjust path if needed)
// require_once 'db.php';

// /**
//  * Resolve logged-in user's status (from session or DB).
//  *
//  * @return string|null user status in lowercase (e.g. "admin", "lawyer", "user") or null if not found
//  */
// function resolve_user_status() {
//     global $conn;

//     // Normalize session key
//     if (empty($_SESSION['user_email']) && !empty($_SESSION['useremail'])) {
//         $_SESSION['user_email'] = $_SESSION['useremail'];
//     }

//     // If already in session
//     if (!empty($_SESSION['user_status'])) {
//         return strtolower((string) $_SESSION['user_status']);
//     }

//     // If email exists in session, fetch status from DB
//     if (!empty($_SESSION['user_email'])) {
//         $email = $_SESSION['user_email'];

//         if ($stmt = $conn->prepare("SELECT `status` FROM `user` WHERE `email` = ? LIMIT 1")) {
//             $stmt->bind_param("s", $email);
//             $stmt->execute();
//             $res = $stmt->get_result();
//             if ($res && $res->num_rows === 1) {
//                 $row = $res->fetch_assoc();
//                 if (!empty($row['status'])) {
//                     $_SESSION['user_status'] = $row['status']; // cache in session
//                     $stmt->close();
//                     return strtolower($row['status']);
//                 }
//             }
//             $stmt->close();
//         }
//     }

//     return null;
// }

// /**
//  * Enforce access control
//  *
//  * @param array $allowed_statuses (case-insensitive list of allowed statuses)
//  */
// function enforce_access(array $allowed_statuses = []) {
//     if (empty($_SESSION['user_email'])) {
//         // Not logged in
//         header("Location: Login.php?error=" . urlencode("Please login first."));
//         exit;
//     }

//     $status = resolve_user_status();
//     if ($status === null) {
//         header("Location: Login.php?error=" . urlencode("Please login first."));
//         exit;
//     }

//     if (!empty($allowed_statuses)) {
//         $allowed_lower = array_map('strtolower', $allowed_statuses);
//         if (!in_array($status, $allowed_lower, true)) {
//             http_response_code(403);
//             echo "Access Denied! You do not have permission to view this page.";
//             exit;
//         }
//     }
// }







// middleware.php - Access control middleware (email is primary key)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// include DB connection (adjust path if needed)
require_once 'db.php';

// ============================================
// 🔥 NEW: 1-HOUR SESSION TIMEOUT CHECK
// ============================================
function check_session_timeout() {
    // If last_activity is not set, set it now
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    $current_time = time();
    $elapsed_time = $current_time - $_SESSION['last_activity'];
    
    // 1 hour = 3600 seconds
    if ($elapsed_time > 1800) {
        // Session expired - destroy it
        session_unset();
        session_destroy();
        
        // Redirect to login with timeout message
        $error_message = "Your session has expired due to 1 hour of inactivity. Please login again.";
        header("Location: Login.php?error=" . urlencode($error_message));
        exit;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = $current_time;
    return true;
}

// ============================================
// আগের function গুলো exactly same রাখলাম
// ============================================

/**
 * Resolve logged-in user's status (from session or DB).
 *
 * @return string|null user status in lowercase (e.g. "admin", "lawyer", "user") or null if not found
 */
function resolve_user_status() {
    global $conn;

    // Normalize session key
    if (empty($_SESSION['user_email']) && !empty($_SESSION['useremail'])) {
        $_SESSION['user_email'] = $_SESSION['useremail'];
    }

    // If already in session
    if (!empty($_SESSION['user_status'])) {
        return strtolower((string) $_SESSION['user_status']);
    }

    // If email exists in session, fetch status from DB
    if (!empty($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];

        if ($stmt = $conn->prepare("SELECT `status` FROM `user` WHERE `email` = ? LIMIT 1")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows === 1) {
                $row = $res->fetch_assoc();
                if (!empty($row['status'])) {
                    $_SESSION['user_status'] = $row['status']; // cache in session
                    $stmt->close();
                    return strtolower($row['status']);
                }
            }
            $stmt->close();
        }
    }

    return null;
}

/**
 * Enforce access control
 *
 * @param array $allowed_statuses (case-insensitive list of allowed statuses)
 */
function enforce_access(array $allowed_statuses = []) {
    // 🔥 STEP 1: Check session timeout (NEW ADDED)
    check_session_timeout();
    
    // STEP 2: Check if user is logged in (Your existing code)
    if (empty($_SESSION['user_email'])) {
        // Not logged in
        header("Location: Login.php?error=" . urlencode("Please login first."));
        exit;
    }

    // STEP 3: Resolve user status (Your existing code)
    $status = resolve_user_status();
    if ($status === null) {
        header("Location: Login.php?error=" . urlencode("Please login first."));
        exit;
    }

    // STEP 4: Check permissions (Your existing code)
    if (!empty($allowed_statuses)) {
        $allowed_lower = array_map('strtolower', $allowed_statuses);
        if (!in_array($status, $allowed_lower, true)) {
            http_response_code(403);
            echo "Access Denied! You do not have permission to view this page.";
            exit;
        }
    }
}