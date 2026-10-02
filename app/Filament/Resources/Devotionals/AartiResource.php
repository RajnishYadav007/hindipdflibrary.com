<?php

namespace App\Filament\Resources\Devotionals;

use App\Filament\Resources\Devotionals\Pages\CreateAarti;
use App\Filament\Resources\Devotionals\Pages\EditAarti;
use App\Filament\Resources\Devotionals\Pages\ListAartis;
use App\Filament\Resources\Devotionals\Schemas\DevotionalForm;
use App\Filament\Resources\Devotionals\Tables\DevotionalsTable;
use App\Models\DevotionalContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class AartiResource extends Resource
{
    protected static ?string $model = DevotionalContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSun;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'आरती (Aarti)';

    protected static ?string $modelLabel = 'आरती';

    protected static ?string $pluralModelLabel = 'आरती संग्रह';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return DevotionalForm::configure($schema, 'aarti');
    }

    public static function table(Table $table): Table
    {
        return DevotionalsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'aarti');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAartis::route('/'),
            'create' => CreateAarti::route('/create'),
            'edit' => EditAarti::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('type', 'aarti')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
