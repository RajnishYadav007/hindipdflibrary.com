<?php

namespace App\Support\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class AudioUploadValidator
{
    public const ALLOWED_EXTENSIONS = ['mp3', 'm4a', 'aac'];

    public const ALLOWED_MIME_TYPES = [
        'audio/mpeg',
        'audio/mp3',
        'audio/mp4',
        'audio/x-m4a',
        'audio/aac',
    ];

    public const MAX_SIZE_KB = 51200; // 50MB

    /**
     * Validate an uploaded audio file.
     *
     * @throws ValidationException
     */
    public static function validate(UploadedFile $file, string $field = 'audio_file'): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                $field => "Invalid audio format (.{$extension}). Allowed audio formats are: MP3, M4A, AAC.",
            ]);
        }

        $mimeType = $file->getMimeType();
        if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            throw ValidationException::withMessages([
                $field => "Invalid MIME type ({$mimeType}). The file does not appear to be a genuine audio stream.",
            ]);
        }

        if ($file->getSize() > (self::MAX_SIZE_KB * 1024)) {
            throw ValidationException::withMessages([
                $field => 'Audio file size exceeds the maximum allowed limit of 50MB.',
            ]);
        }

        // Deep inspect magic bytes using finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        if (! in_array($realMime, self::ALLOWED_MIME_TYPES, true)) {
            // Some AAC files report as audio/aac or video/mp4 for m4a
            if (! str_starts_with($realMime, 'audio/') && $realMime !== 'video/mp4') {
                throw ValidationException::withMessages([
                    $field => 'Audio file content validation failed. Executable or corrupted files are rejected.',
                ]);
            }
        }
    }
}
