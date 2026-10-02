<?php

namespace App\Filament\Resources\Devotionals;

use App\Filament\Resources\Devotionals\Pages\CreateChalisa;
use App\Filament\Resources\Devotionals\Pages\EditChalisa;
use App\Filament\Resources\Devotionals\Pages\ListChalisas;
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

class ChalisaResource extends Resource
{
    protected static ?string $model = DevotionalContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'चालीसा (Chalisa)';

    protected static ?string $modelLabel = 'चालीसा';

    protected static ?string $pluralModelLabel = 'चालीसा संग्रह';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Schema $schema): Schema
    {
        return DevotionalForm::configure($schema, 'chalisa');
    }

    public static function table(Table $table): Table
    {
        return DevotionalsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', 'chalisa');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChalisas::route('/'),
            'create' => CreateChalisa::route('/create'),
            'edit' => EditChalisa::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->where('type', 'chalisa')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
