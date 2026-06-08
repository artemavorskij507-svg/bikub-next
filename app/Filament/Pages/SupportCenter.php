<?php

namespace App\Filament\Pages;

class SupportCenter extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Support Center';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Support Center';

    public function getModuleKey(): string
    {
        return 'support';
    }
}
