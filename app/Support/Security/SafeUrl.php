<?php

namespace App\Support\Security;

class SafeUrl
{
    /**
     * Determine if a URL is safe for external redirection or link output.
     */
    public static function isSafe(?string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $trimmed = trim($url);

        // Disallow dangerous or local protocol schemes
        if (preg_match('/^(javascript|data|file|phar|php|view-source|about|blob):/i', $trimmed)) {
            return false;
        }

        // Must strictly start with http:// or https://
        if (! preg_match('/^https?:\/\//i', $trimmed)) {
            return false;
        }

        // Validate structure via filter_var
        if (! filter_var($trimmed, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($trimmed, PHP_URL_HOST);
        if (empty($host)) {
            return false;
        }

        // Block internal loopback and common localhost addresses
        if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1' || $host === '0.0.0.0') {
            return false;
        }

        // Block private and reserved IP ranges (SSRF protection)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Alias for isSafe.
     */
    public static function isValid(?string $url): bool
    {
        return static::isSafe($url);
    }

    /**
     * Sanitize a URL. Returns the validated URL or null if unsafe.
     */
    public static function sanitize(?string $url): ?string
    {
        if (! self::isSafe($url)) {
            return null;
        }

        return htmlspecialchars(trim((string) $url), ENT_QUOTES, 'UTF-8');
    }
}
