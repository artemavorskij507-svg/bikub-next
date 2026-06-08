<?php

namespace App\Filament\Pages;

class DispatchCenter extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Dispatch Center';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Dispatch Center';

    public function getModuleKey(): string
    {
        return 'dispatch';
    }
}
