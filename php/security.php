<?php
require_once __DIR__ . '/config.php';

class Security {

    // ── Session Init ─────────────────────────────────
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Strict');
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }
        // Rotate session ID occasionally
        if (!isset($_SESSION['_init'])) {
            session_regenerate_id(true);
            $_SESSION['_init'] = time();
        }
    }

    // ── Encrypt/Decrypt batch ID ─────────────────────
    public static function encryptId(int $id): string {
        $plain = 'ID:' . $id . ':' . substr(md5(ENCRYPT_KEY . $id), 0, 8);
        return self::encrypt($plain);
    }

    public static function decryptId(string $token): ?int {
        $plain = self::decrypt($token);
        if (!$plain) return null;
        if (preg_match('/^ID:(\d+):([a-f0-9]{8})$/', $plain, $m)) {
            $id = (int)$m[1];
            $check = substr(md5(ENCRYPT_KEY . $id), 0, 8);
            if ($check === $m[2]) return $id;
        }
        return null;
    }

    // ── Encrypt/Decrypt subject name ─────────────────
    public static function encryptSubject(string $subject): string {
        return self::encrypt('S:' . $subject);
    }

    public static function decryptSubject(string $token): string|false {
        $plain = self::decrypt($token);
        if (!$plain) return false;
        if (str_starts_with($plain, 'S:')) return substr($plain, 2);
        return false;
    }

    // ── Encrypt/Decrypt video URL ────────────────────
    public static function encryptVideo(string $url, int $batchId): string {
        $plain = 'V:' . $batchId . ':' . $url;
        return self::encrypt($plain);
    }

    public static function decryptVideo(string $token): ?string {
        $plain = self::decrypt($token);
        if (!$plain) return null;
        if (preg_match('/^V:\d+:(.+)$/', $plain, $m)) {
            return $m[1];
        }
        return null;
    }

    // ── Core AES-256-CBC encrypt/decrypt ─────────────
    private static function encrypt(string $plain): string {
        $key = hash('sha256', ENCRYPT_KEY, true);
        $iv  = substr(hash('sha256', ENCRYPT_IV, true), 0, 16);
        $enc = openssl_encrypt($plain, 'AES-256-CBC', $key, 0, $iv);
        // URL-safe base64
        return rtrim(strtr(base64_encode($enc), '+/', '-_'), '=');
    }

    private static function decrypt(string $token): ?string {
        if (empty($token)) return null;
        try {
            // Restore base64
            $b64 = base64_decode(strtr($token, '-_', '+/') . str_repeat('=', 4 - strlen($token) % 4));
            $key = hash('sha256', ENCRYPT_KEY, true);
            $iv  = substr(hash('sha256', ENCRYPT_IV, true), 0, 16);
            $dec = openssl_decrypt($b64, 'AES-256-CBC', $key, 0, $iv);
            return $dec ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ── Sanitize output ──────────────────────────────
    public static function h(string $str): string {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
