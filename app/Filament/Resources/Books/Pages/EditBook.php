<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use App\Filament\Resources\Books\Concerns\HandlesBookMedia;
use App\Models\Book;
use App\Services\Media\BookCoverService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBook extends EditRecord
{
    use HandlesBookMedia;

    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_reader')
                ->label('Open Interactive Reader')
                ->icon('heroicon-m-book-open')
                ->color('info')
                ->url(fn (): string => route('books.interactive_reader', ['slug' => $this->record->slug]))
                ->openUrlInNewTab(),

            Action::make('view_public')
                ->label('View Public Page')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (): string => route('books.show', ['slug' => $this->record->slug]))
                ->openUrlInNewTab(),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var Book $book */
        $book = $this->record;

        return $this->populateBookMedia($book, $data);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $coverImage = $data['cover_image'] ?? null;
        $pdfFile = $data['pdf_file'] ?? null;
        unset($data['cover_image'], $data['pdf_file']);

        $record->update($data);

        /** @var Book $book */
        $book = $record;

        if (empty($coverImage) && ! $book->media()->where('collection_name', 'cover')->exists()) {
            $coverImage = app(BookCoverService::class)->autoGenerateCover($book, $pdfFile);
        }

        $this->syncBookMedia($book, $coverImage, $pdfFile);

        return $book;
    }
}
