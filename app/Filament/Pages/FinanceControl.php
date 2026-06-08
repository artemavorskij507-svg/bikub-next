<?php

namespace App\Filament\Pages;

class FinanceControl extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Finance Control';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Finance Control';

    public function getModuleKey(): string
    {
        return 'finance';
    }
}
