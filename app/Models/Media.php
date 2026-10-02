<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $attributes = [
        'size' => 0,
        'manipulations' => '[]',
        'custom_properties' => '[]',
        'generated_conversions' => '[]',
        'responsive_images' => '[]',
    ];

    protected static function booted(): void
    {
        static::creating(function (Media $media) {
            if (empty($media->uuid)) {
                $media->uuid = (string) Str::uuid();
            }

            if ((empty($media->size) || $media->size === 0) && ! empty($media->file_name) && ! empty($media->disk)) {
                try {
                    if (Storage::disk($media->disk)->exists($media->file_name)) {
                        $media->size = (int) Storage::disk($media->disk)->size($media->file_name);
                        if (empty($media->mime_type)) {
                            $media->mime_type = Storage::disk($media->disk)->mimeType($media->file_name);
                        }
                    }
                } catch (\Throwable) {
                    // Fallback to 0
                }
            }

            if ($media->size === null) {
                $media->size = 0;
            }
        });
    }

    protected $fillable = [
        'model_type',
        'model_id',
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'order_column' => 'integer',
            'manipulations' => 'array',
            'custom_properties' => 'array',
            'generated_conversions' => 'array',
            'responsive_images' => 'array',
        ];
    }

    /**
     * Polymorphic parent model (e.g. Book, Author).
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
