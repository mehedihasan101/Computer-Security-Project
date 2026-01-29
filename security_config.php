<?php
// security_config.php

class SecurityLogger {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Log security events
     */
    public function logEvent($action, $description, $user_email = null) {
        $user_id = $user_email;
        $ip_address = $this->getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        $stmt = $this->conn->prepare("
            INSERT INTO security_log (user_id, user_email, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("ssssss", $user_id, $user_email, $action, $description, $ip_address, $user_agent);
        
        try {
            return $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // If foreign key constraint fails, insert with NULL user_email
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $stmt = $this->conn->prepare("
                    INSERT INTO security_log (user_id, user_email, action, description, ip_address, user_agent) 
                    VALUES (?, NULL, ?, ?, ?, ?)
                ");
                $stmt->bind_param("sssss", $user_id, $action, $description, $ip_address, $user_agent);
                return $stmt->execute();
            }
            throw $e; // Re-throw if it's a different error
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip_keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Log login attempts
     */
    public function logLoginAttempt($email, $success, $reason = '') {
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        $description = $success ? 
            "User logged in successfully" : 
            "Failed login attempt for email: $email. Reason: $reason";
        
        return $this->logEvent($action, $description, $success ? $email : null);
    }
    
    /**
     * Log logout events
     */
    public function logLogout($user_email, $username = '') {
        $description = "User logged out: " . ($username ?: 'Unknown User');
        return $this->logEvent('LOGOUT', $description, $user_email);
    }
    
    /**
     * Log user registration
     */
    public function logUserRegistration($email, $username = '') {
        $description = "New user registered: $email";
        return $this->logEvent('USER_REGISTERED', $description, $email);
    }
    
    /**
     * Log password changes
     */
    public function logPasswordChange($user_email, $username = '') {
        $description = "Password changed for user: $username";
        return $this->logEvent('PASSWORD_CHANGE', $description, $user_email);
    }
    
    /**
     * Log profile updates
     */
    public function logProfileUpdate($user_email, $username = '') {
        $description = "Profile updated for user: $username";
        return $this->logEvent('PROFILE_UPDATE', $description, $user_email);
    }
    
    /**
     * Log suspicious activities
     */
    public function logSuspiciousActivity($activity, $user_email = null) {
        return $this->logEvent('SUSPICIOUS_ACTIVITY', $activity, $user_email);
    }
    
    /**
     * Get security logs with pagination
     */
    public function getSecurityLogs($limit = 50, $offset = 0, $filter = '') {
        $logs = [];
        
        if (!empty($filter)) {
            $stmt = $this->conn->prepare("
                SELECT * FROM security_log 
                WHERE action LIKE ? OR description LIKE ? OR user_email LIKE ?
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $search_filter = "%$filter%";
            $stmt->bind_param("sssii", $search_filter, $search_filter, $search_filter, $limit, $offset);
        } else {
            $stmt = $this->conn->prepare("
                SELECT * FROM security_log 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param("ii", $limit, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }
        
        return $logs;
    }
    
    /**
     * Get total log count for pagination
     */
    public function getTotalLogCount($filter = '') {
        if (!empty($filter)) {
            $stmt = $this->conn->prepare("
                SELECT COUNT(*) as total FROM security_log 
                WHERE action LIKE ? OR description LIKE ? OR user_email LIKE ?
            ");
            $search_filter = "%$filter%";
            $stmt->bind_param("sss", $search_filter, $search_filter, $search_filter);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM security_log");
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }
}

// Security configuration functions
class SecurityConfig {
    
    /**
     * Set security headers
     */
    public static function setSecurityHeaders() {
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        
        // Only set HSTS header if using HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }
    }
    
    /**
     * Configure secure session settings
     * This should be called BEFORE session_start()
     */
    public static function configureSecureSession() {
        // Only configure session if it hasn't been started yet
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            ini_set('session.gc_maxlifetime', 60); // 1 hour in seconds

            session_set_cookie_params([
                'lifetime' => 60, 
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'] ?? '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Check for SQL injection patterns
     */
    public static function detectSQLInjection($input) {
        $sql_patterns = [
            '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|ALTER|CREATE)\b/i',
            '/\b(OR|AND)\s+[\d\'\"]/i',
            '/--|\/\*|\*\//',
            '/;\s*(\w+)/'
        ];
        
        foreach ($sql_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check for XSS patterns
     */
    public static function detectXSS($input) {
        $xss_patterns = [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe\b[^>]*>(.*?)<\/iframe>/is',
            '/<object\b[^>]*>(.*?)<\/object>/is'
        ];
        
        foreach ($xss_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }
}

// Only set security headers if headers haven't been sent yet
if (!headers_sent()) {
    SecurityConfig::setSecurityHeaders();
}

// Disable dangerous PHP functions
@ini_set('disable_functions', 'exec,passthru,shell_exec,system,proc_open,popen,show_source');

?>