<?php

namespace App\Enums;

enum CopyrightStatus: string
{
    case PublicDomain = 'public_domain';
    case OpenAccess = 'open_access';
    case CreativeCommons = 'creative_commons';
    case Government = 'government';
    case PermissionGranted = 'permission_granted';
    case PublisherAuthorized = 'publisher_authorized';
    case ExternalSourceOnly = 'external_source_only';
    case Unknown = 'unknown';

    /**
     * Whether the platform is legally permitted to store a private PDF for this book.
     */
    public function canHostPdf(): bool
    {
        return match ($this) {
            self::PublicDomain,
            self::OpenAccess,
            self::CreativeCommons,
            self::Government,
            self::PermissionGranted,
            self::PublisherAuthorized => true,
            self::ExternalSourceOnly,
            self::Unknown => false,
        };
    }

    /**
     * Whether readers are legally permitted to download the PDF file directly.
     */
    public function canDownloadPdf(): bool
    {
        return $this->canHostPdf();
    }

    /**
     * Whether this book is strictly an external referral/discovery record.
     */
    public function isExternalSourceOnly(): bool
    {
        return $this === self::ExternalSourceOnly;
    }

    /**
     * Human-readable localized label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PublicDomain => 'Public Domain',
            self::OpenAccess => 'Open Access',
            self::CreativeCommons => 'Creative Commons',
            self::Government => 'Government / Public Record',
            self::PermissionGranted => 'Permission Granted by Author',
            self::PublisherAuthorized => 'Authorized by Publisher',
            self::ExternalSourceOnly => 'External Source Only (No Local Hosting)',
            self::Unknown => 'Rights Under Review',
        };
    }
}
