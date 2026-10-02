<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TextToSpeechController extends Controller
{
    /**
     * Stream spoken Hindi audio for a sentence with disk caching.
     */
    public function stream(Request $request): Response|BinaryFileResponse
    {
        $text = trim((string) $request->query('text', ''));
        if ($text === '') {
            return response('Text is required', 400);
        }

        $text = mb_substr($text, 0, 350);
        $lang = preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', (string) $request->query('lang', 'hi'))
            ? (string) $request->query('lang', 'hi')
            : 'hi';

        $cacheDir = storage_path('app/tts_cache');
        if (! File::exists($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        $cacheFile = $cacheDir.DIRECTORY_SEPARATOR.$lang.'_'.md5($text).'.mp3';

        if (File::exists($cacheFile) && File::size($cacheFile) > 0) {
            return response()->file($cacheFile, [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'public, max-age=604800, immutable',
            ]);
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(8)
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')
                ->get('https://translate.google.com/translate_tts', [
                    'ie' => 'UTF-8',
                    'tl' => $lang,
                    'client' => 'tw-ob',
                    'q' => $text,
                ]);

            if ($response->successful() && strlen($response->body()) > 500) {
                File::put($cacheFile, $response->body());

                return response()->file($cacheFile, [
                    'Content-Type' => 'audio/mpeg',
                    'Cache-Control' => 'public, max-age=604800, immutable',
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return response('TTS generation failed', 503);
    }
}
