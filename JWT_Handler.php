<?php
// JWT_Handler.php

require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWT_Handler {
    private $secret_key;
    private $algorithm;
    private $issuer;
    
    public function __construct() {
        // 🔥 SECRET_KEY environment variable থেকে নিন বা এখানে সেট করুন
        $this->secret_key = getenv('JWT_SECRET_KEY') ?: 'your-very-long-secret-key-minimum-32-characters-long-here';
        $this->algorithm = 'HS256';
        $this->issuer = 'alliance-system';
        
        // Security check
        if (strlen($this->secret_key) < 32) {
            throw new Exception('JWT secret key must be at least 32 characters long');
        }
    }
    
 
     // JWT Token Create
    public function create_token($user_data) {
        $issued_at = time();
        $expire = $issued_at + 3600; // 1 hour
        $payload = [
            'iss' => $this->issuer,       // Issuer
            'iat' => $issued_at,         // Issued at
            'exp' => $expire,            // Expiration time
            'sub' => $user_data['email'], // Subject (user email)
            'data' => [                   // User data
                'email' => $user_data['email'],
                'role' => $user_data['role'],
                'fullname' => $user_data['fullname'] ?? '',
                'user_id' => $user_data['id'] ?? 0
            ]
        ]; 
        return JWT::encode($payload, $this->secret_key, $this->algorithm);
    }

     //JWT Token verify 
    public function verify_token($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secret_key, $this->algorithm));
            return (array) $decoded;
        } catch (Exception $e) {
            // Log the error
            error_log("JWT Verification Failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Token থেকে user data পাওয়া
     */
    public function get_user_from_token($token) {
        $payload = $this->verify_token($token);
        if ($payload && isset($payload['data'])) {
            return (array) $payload['data'];
        }
        return null;
    }
    
    /**
     * Token refresh করার জন্য (যদি দরকার হয়)
     */
    public function refresh_token($old_token) {
        $payload = $this->verify_token($old_token);
        if (!$payload) {
            return null;
        }
        
        // Check if token can be refreshed (not expired for too long)
        $issued_at = $payload['iat'];
        if (time() - $issued_at > 86400) { // 24 hours limit
            return null;
        }
        
        // Create new token
        return $this->create_token((array) $payload['data']);
    }
}