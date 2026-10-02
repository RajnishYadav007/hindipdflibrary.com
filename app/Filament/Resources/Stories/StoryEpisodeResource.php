<?php

namespace App\Filament\Resources\Stories;

use App\Filament\Resources\Stories\Pages\CreateStoryEpisode;
use App\Filament\Resources\Stories\Pages\EditStoryEpisode;
use App\Filament\Resources\Stories\Pages\ListStoryEpisodes;
use App\Filament\Resources\Stories\Schemas\StoryEpisodeForm;
use App\Filament\Resources\Stories\Tables\StoryEpisodesTable;
use App\Models\StoryEpisode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class StoryEpisodeResource extends Resource
{
    protected static ?string $model = StoryEpisode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'कहानी अध्याय (Story Episodes)';

    protected static ?string $modelLabel = 'कहानी अध्याय';

    protected static ?string $pluralModelLabel = 'कहानी अध्याय संग्रह';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return StoryEpisodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StoryEpisodesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStoryEpisodes::route('/'),
            'create' => CreateStoryEpisode::route('/create'),
            'edit' => EditStoryEpisode::route('/{record}/edit'),
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
