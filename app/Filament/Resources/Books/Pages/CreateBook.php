<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use App\Filament\Resources\Books\Concerns\HandlesBookMedia;
use App\Models\Book;
use App\Services\Media\BookCoverService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBook extends CreateRecord
{
    use HandlesBookMedia;

    protected static string $resource = BookResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        $pdfFile = $data['pdf_file'] ?? null;
        unset($data['cover_image'], $data['pdf_file']);

        /** @var Book $book */
        $book = static::getModel()::create($data);

        if (empty($coverImage)) {
            $coverImage = app(BookCoverService::class)->autoGenerateCover($book, $pdfFile);
        }

        $this->syncBookMedia($book, $coverImage, $pdfFile);

        return $book;
    }
}
