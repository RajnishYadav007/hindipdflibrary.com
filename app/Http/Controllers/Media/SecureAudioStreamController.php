<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureAudioStreamController extends Controller
{
    /**
     * Stream protected/public audio media files with HTTP Range support for audio seeking.
     */
    public function stream(Request $request, string $uuid)
    {
        $media = Media::where('uuid', $uuid)
            ->orWhere('id', $uuid)
            ->firstOrFail();

        // Check authorization if parent is not published
        $parent = $media->model;
        if ($parent && isset($parent->status) && $parent->status !== 'published') {
            $user = $request->user();
            $canAccess = $user && (
                $user->is_admin ||
                (isset($parent->submitted_by) && $parent->submitted_by === $user->id)
            );

            if (! $canAccess) {
                abort(403, 'Unauthorized access to unpublished audio.');
            }
        }

        $disk = Storage::disk($media->disk);
        if (! $disk->exists($media->file_name)) {
            abort(404, 'Audio file not found.');
        }

        $path = $disk->path($media->file_name);
        $fileSize = (int) $disk->size($media->file_name);
        $mimeType = $media->mime_type ?: 'audio/mpeg';

        $range = $request->header('Range');
        if (! $range) {
            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public, max-age=31536000',
            ]);
        }

        // Handle HTTP Range Requests for instant audio seeking
        [$unit, $rangeSpec] = explode('=', $range, 2);
        if ($unit !== 'bytes') {
            return response('', 416)->header('Content-Range', "bytes */{$fileSize}");
        }

        [$startStr, $endStr] = explode('-', $rangeSpec, 2);
        $start = (int) $startStr;
        $end = ($endStr !== '') ? (int) $endStr : $fileSize - 1;

        if ($start > $end || $start >= $fileSize) {
            return response('', 416)->header('Content-Range', "bytes */{$fileSize}");
        }

        $length = $end - $start + 1;
        $stream = fopen($path, 'rb');
        fseek($stream, $start);

        return new StreamedResponse(function () use ($stream, $length) {
            $remaining = $length;
            $bufferSize = 8192;

            while (! feof($stream) && $remaining > 0) {
                $bytesToRead = min($bufferSize, $remaining);
                $chunk = fread($stream, $bytesToRead);
                echo $chunk;
                flush();
                $remaining -= strlen($chunk);
            }

            fclose($stream);
        }, 206, [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache',
        ]);
    }
}
