<?php

namespace Tests\Feature\Analytics;

use App\Support\Analytics\DeviceClassifier;
use App\Support\Analytics\ReferrerClassifier;
use Tests\TestCase;

class ClassificationTest extends TestCase
{
    public function test_referrer_classification(): void
    {
        // Direct
        $this->assertSame(
            ReferrerClassifier::SOURCE_DIRECT,
            ReferrerClassifier::classify(null)
        );
        $this->assertSame(
            ReferrerClassifier::SOURCE_DIRECT,
            ReferrerClassifier::classify('')
        );

        // Google
        $this->assertSame(
            ReferrerClassifier::SOURCE_GOOGLE,
            ReferrerClassifier::classify('https://www.google.com/search?q=hindi+books')
        );
        $this->assertSame(
            ReferrerClassifier::SOURCE_GOOGLE,
            ReferrerClassifier::classify('https://www.google.co.in/')
        );

        // Social
        $this->assertSame(
            ReferrerClassifier::SOURCE_SOCIAL,
            ReferrerClassifier::classify('https://facebook.com/groups/books')
        );
        $this->assertSame(
            ReferrerClassifier::SOURCE_SOCIAL,
            ReferrerClassifier::classify('https://t.co/xyz123')
        );
        $this->assertSame(
            ReferrerClassifier::SOURCE_SOCIAL,
            ReferrerClassifier::classify('https://www.instagram.com/')
        );
        $this->assertSame(
            ReferrerClassifier::SOURCE_SOCIAL,
            ReferrerClassifier::classify('https://web.whatsapp.com/')
        );

        // Referral
        $this->assertSame(
            ReferrerClassifier::SOURCE_REFERRAL,
            ReferrerClassifier::classify('https://someblog.org/top-10-hindi-books')
        );

        // Internal app host is classified as other
        $this->assertSame(
            ReferrerClassifier::SOURCE_OTHER,
            ReferrerClassifier::classify('https://hindipdflibrary.com/search', 'hindipdflibrary.com')
        );
    }

    public function test_device_classification(): void
    {
        // Mobile user agents
        $iphoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1';
        $androidMobileUA = 'Mozilla/5.0 (Linux; Android 13; SM-S908B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36';

        $this->assertSame(DeviceClassifier::DEVICE_MOBILE, DeviceClassifier::classifyDevice($iphoneUA));
        $this->assertSame(DeviceClassifier::DEVICE_MOBILE, DeviceClassifier::classifyDevice($androidMobileUA));

        // Tablet user agents
        $ipadUA = 'Mozilla/5.0 (iPad; CPU OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1';
        $androidTabletUA = 'Mozilla/5.0 (Linux; Android 12; SM-X900) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36';

        $this->assertSame(DeviceClassifier::DEVICE_TABLET, DeviceClassifier::classifyDevice($ipadUA));
        $this->assertSame(DeviceClassifier::DEVICE_TABLET, DeviceClassifier::classifyDevice($androidTabletUA));

        // Desktop user agents
        $windowsDesktopUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $macDesktopUA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';
        $linuxDesktopUA = 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0';

        $this->assertSame(DeviceClassifier::DEVICE_DESKTOP, DeviceClassifier::classifyDevice($windowsDesktopUA));
        $this->assertSame(DeviceClassifier::DEVICE_DESKTOP, DeviceClassifier::classifyDevice($macDesktopUA));
        $this->assertSame(DeviceClassifier::DEVICE_DESKTOP, DeviceClassifier::classifyDevice($linuxDesktopUA));

        // Unknown
        $this->assertSame(DeviceClassifier::DEVICE_UNKNOWN, DeviceClassifier::classifyDevice('CustomClient/1.0'));
        $this->assertSame(DeviceClassifier::DEVICE_UNKNOWN, DeviceClassifier::classifyDevice(null));
    }

    public function test_browser_and_os_detection(): void
    {
        $windowsChrome = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
        $this->assertSame('Chrome', DeviceClassifier::detectBrowser($windowsChrome));
        $this->assertSame('Windows', DeviceClassifier::detectOs($windowsChrome));

        $macSafari = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';
        $this->assertSame('Safari', DeviceClassifier::detectBrowser($macSafari));
        $this->assertSame('macOS', DeviceClassifier::detectOs($macSafari));

        $linuxFirefox = 'Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0';
        $this->assertSame('Firefox', DeviceClassifier::detectBrowser($linuxFirefox));
        $this->assertSame('Linux', DeviceClassifier::detectOs($linuxFirefox));
    }
}
