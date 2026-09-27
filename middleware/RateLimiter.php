<?php
// middleware/RateLimiter.php

class RateLimiter {
    public static function check(PDO $pdo, string $ip, string $endpoint = 'generate', int $maxRequests = 6, int $windowSeconds = 300): bool {
        $now = time();
        $cutoff = $now - $windowSeconds;

        try {
            // 1. Purge old records
            $cleanStmt = $pdo->prepare("DELETE FROM api_rate_limits WHERE request_time < ?");
            $cleanStmt->execute([$cutoff]);

            // 2. Count requests in active window
            $countStmt = $pdo->prepare("SELECT COUNT(*) FROM api_rate_limits WHERE ip_address = ? AND endpoint = ? AND request_time >= ?");
            $countStmt->execute([$ip, $endpoint, $cutoff]);
            $currentCount = (int)$countStmt->fetchColumn();

            if ($currentCount >= $maxRequests) {
                return false; // Rate limit exceeded
            }

            // 3. Record this request
            $insStmt = $pdo->prepare("INSERT INTO api_rate_limits (ip_address, endpoint, request_time) VALUES (?, ?, ?)");
            $insStmt->execute([$ip, $endpoint, $now]);

            return true;
        } catch (Throwable $e) {
            // Fail open on rate limiter error to ensure availability
            error_log("RateLimiter error: " . $e->getMessage());
            return true;
        }
    }
}
