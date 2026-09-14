<?php

namespace App\Support\Analytics;

class DeviceClassifier
{
    public const DEVICE_MOBILE = 'mobile';

    public const DEVICE_TABLET = 'tablet';

    public const DEVICE_DESKTOP = 'desktop';

    public const DEVICE_UNKNOWN = 'unknown';

    /**
     * Classify User-Agent into device type.
     */
    public static function classifyDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return self::DEVICE_UNKNOWN;
        }

        // Check for Tablet first
        if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|silk)/i', $userAgent)) {
            return self::DEVICE_TABLET;
        }

        // Check for Mobile
        if (preg_match('/(mobile|iphone|ipod|android.*mobile|blackberry|iemobile|opera mini|webos|windows phone)/i', $userAgent)) {
            return self::DEVICE_MOBILE;
        }

        // Check for Desktop signatures
        if (preg_match('/(windows nt|macintosh|mac os x|linux|x11|cros)/i', $userAgent)) {
            return self::DEVICE_DESKTOP;
        }

        return self::DEVICE_UNKNOWN;
    }

    /**
     * Extract browser family name.
     */
    public static function detectBrowser(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        if (preg_match('/edg/i', $userAgent)) {
            return 'Edge';
        }
        if (preg_match('/chrome|crios/i', $userAgent) && ! preg_match('/opr|opera/i', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/firefox|fxios/i', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/safari/i', $userAgent) && ! preg_match('/chrome|crios|android/i', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/opr|opera/i', $userAgent)) {
            return 'Opera';
        }

        return 'Other';
    }

    /**
     * Extract operating system family name.
     */
    public static function detectOs(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Unknown';
        }

        if (preg_match('/windows/i', $userAgent)) {
            return 'Windows';
        }
        if (preg_match('/android/i', $userAgent)) {
            return 'Android';
        }
        if (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            return 'iOS';
        }
        if (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'macOS';
        }
        if (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        }

        return 'Other';
    }
}
