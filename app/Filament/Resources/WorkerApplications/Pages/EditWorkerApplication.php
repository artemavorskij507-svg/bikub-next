<?php
namespace App\Filament\Resources\WorkerApplications\Pages;
use App\Filament\Resources\WorkerApplications\WorkerApplicationResource;
use App\Services\Workers\WorkerOnboardingService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
class EditWorkerApplication extends EditRecord {
 protected static string $resource=WorkerApplicationResource::class;
 protected function getHeaderActions():array{return [
  Action::make('approve')->disabled(fn()=>!$this->record->user_id||$this->record->status!=='submitted')->action(function(){app(WorkerOnboardingService::class)->approve($this->record,auth()->user(),'Approved through reviewed application.');Notification::make()->title('Application approved')->success()->send();$this->refreshFormData(['status']);}),
  Action::make('reject')->schema([Textarea::make('reason')->required()])->action(function(array $data){app(WorkerOnboardingService::class)->reject($this->record,auth()->user(),$data['reason']);Notification::make()->title('Application rejected')->warning()->send();$this->refreshFormData(['status','decision_reason']);}),
 ];}}
