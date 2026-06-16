<?php

namespace App\Filament\Resources\PublicSitePages\Pages;

use App\Filament\Resources\PublicSitePages\PublicSitePageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPublicSitePage extends EditRecord
{
    protected static string $resource = PublicSitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn () => route('admin.public-site-preview', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
