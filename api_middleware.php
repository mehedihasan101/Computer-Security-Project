<?php
// api_middleware.php - API Security Middleware

require_once 'db.php';

// Force JSON response
header("Content-Type: application/json");


 //🔐 API Token Authentication (Bearer Token)
function enforce_api_auth() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Authorization header missing"]);
        exit;
    }
    // Extract Bearer token
    $token = str_replace("Bearer ", "", $headers['Authorization']);
    $token = trim($token);

    if (empty($token)) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid token"]);
        exit;
    }
    global $conn;
    // Validate token from DB
    $stmt = $conn->prepare("
        SELECT u.email, u.status 
        FROM api_tokens t
        JOIN user u ON u.id = t.user_id
        WHERE t.token = ? AND t.is_active = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows !== 1) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized API token"]);
        exit;
    }
    // Store user info for API usage
    $user = $res->fetch_assoc();
    $_SERVER['API_USER_EMAIL']  = $user['email'];
    $_SERVER['API_USER_STATUS'] = strtolower($user['status']);
}

/**
 * 🔒 Role-based API Authorization
 */
function enforce_api_role(array $allowed_roles = []) {
    if (empty($allowed_roles)) return;

    $role = $_SERVER['API_USER_STATUS'] ?? null;
    if (!$role || !in_array($role, array_map('strtolower', $allowed_roles))) {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden: insufficient permissions"]);
        exit;
    }
}

/**
 * 🚦 Basic Rate Limiting (per IP)
 */
function api_rate_limit($maxRequests = 100, $seconds = 60) {
    session_start();

    $ip  = $_SERVER['REMOTE_ADDR'];
    $key = "api_rate_$ip";

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
        return;
    }

    if (time() - $_SESSION[$key]['time'] < $seconds) {
        $_SESSION[$key]['count']++;
        if ($_SESSION[$key]['count'] > $maxRequests) {
            http_response_code(429);
            echo json_encode(["error" => "Too many API requests"]);
            exit;
        }
    } else {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
    }
}
