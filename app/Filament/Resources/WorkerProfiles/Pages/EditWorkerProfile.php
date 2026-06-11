<?php
namespace App\Filament\Resources\WorkerProfiles\Pages;
use App\Filament\Resources\WorkerProfiles\WorkerProfileResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
class EditWorkerProfile extends EditRecord { protected static string $resource = WorkerProfileResource::class; protected function getHeaderActions(): array { return [
 Action::make('online')->visible(fn()=>$this->record->status==='approved')->action(fn()=>app(\App\Services\Workers\WorkerAvailabilityService::class)->setOnline($this->record->user,'Set online by admin.')),
 Action::make('offline')->action(fn()=>app(\App\Services\Workers\WorkerAvailabilityService::class)->setOffline($this->record->user,'Set offline by admin.')),
 Action::make('suspend')->schema([\Filament\Forms\Components\Textarea::make('reason')->required()])->action(fn(array $data)=>app(\App\Services\Workers\WorkerOnboardingService::class)->suspendProfile($this->record,auth()->user(),$data['reason'])),
 ]; } }
