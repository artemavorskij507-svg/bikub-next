<?php

namespace App\Filament\Resources\ClassifiedListings\Pages;

use App\Filament\Resources\ClassifiedListings\ClassifiedListingResource;
use App\Models\ClassifiedListing;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateClassifiedListing extends CreateRecord
{
    protected static string $resource = ClassifiedListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['listing_number'] ??= $this->generateNumber();
        $data['slug'] ??= Str::slug($data['title'] ?? 'classified') ?: Str::random(8);
        $data['user_id'] ??= auth()->id();

        return $data;
    }

    private function generateNumber(): string
    {
        do {
            $number = 'CLS-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (ClassifiedListing::where('listing_number', $number)->exists());

        return $number;
    }
}
