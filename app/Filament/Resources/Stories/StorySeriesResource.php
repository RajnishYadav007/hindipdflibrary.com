<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStorySeries;
use App\Filament\Resources\Stories\Pages\EditStorySeries;
use App\Filament\Resources\Stories\Pages\ListStorySeries;
use App\Filament\Resources\Stories\Schemas\StorySeriesForm;
use App\Filament\Resources\Stories\Tables\StorySeriesTable;
use App\Models\StorySeries;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StorySeriesResource extends Resource
{
    protected static ?string $model = StorySeries::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'बाल कथा श्रृंखला (Story Series)';

    protected static ?string $modelLabel = 'कथा श्रृंखला';

    protected static ?string $pluralModelLabel = 'बाल कथा श्रृंखलाएं';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return StorySeriesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StorySeriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStorySeries::route('/'),
            'create' => CreateStorySeries::route('/create'),
            'edit' => EditStorySeries::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
