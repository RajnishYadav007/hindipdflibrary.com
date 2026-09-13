<?php

namespace App\Filament\Resources\Seo;

use App\Filament\Resources\Seo\Pages\CreateSeoMeta;
use App\Filament\Resources\Seo\Pages\EditSeoMeta;
use App\Filament\Resources\Seo\Pages\ListSeoMetas;
use App\Filament\Resources\Seo\Schemas\SeoMetaForm;
use App\Filament\Resources\Seo\Tables\SeoMetasTable;
use App\Models\SeoMeta;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SeoMetaResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string|UnitEnum|null $navigationGroup = 'Media & SEO';

    protected static ?string $navigationLabel = 'SEO Directives';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SeoMetaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeoMetasTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoMetas::route('/'),
            'create' => CreateSeoMeta::route('/create'),
            'edit' => EditSeoMeta::route('/{record}/edit'),
        ];
    }
}
