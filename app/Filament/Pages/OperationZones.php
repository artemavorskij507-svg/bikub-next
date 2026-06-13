<?php

namespace App\Filament\Pages;

use App\Models\OperationZone;
use App\Services\Operations\OperationZoneService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class OperationZones extends AdminOsModulePage
{
    public ?int $selectedZoneId = null;
    public string $name = '';
    public string $type = 'service_area';
    public float $latitude = 68.4385;
    public float $longitude = 17.4272;
    public int $radius = 500;
    public string $note = '';
    public string $reason = '';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-europe-africa';
    protected static ?string $navigationLabel = 'Service Zones';
    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';
    protected static ?int $navigationSort = 21;
    protected static ?string $slug = 'operation-zones';
    protected string $view = 'filament.pages.operation-zones';

    public function getModuleKey(): string { return 'dispatch'; }
    public function getHeading(): string|Htmlable { return ''; }

    public function mount(): void { $this->selectedZoneId = OperationZone::latest()->value('id'); }
    public function selectZone(int $id): void { $this->selectedZoneId = OperationZone::findOrFail($id)->id; }

    public function createZone(): void
    {
        $this->validate(['name'=>'required|max:150','type'=>'required','latitude'=>'required|numeric|between:-90,90','longitude'=>'required|numeric|between:-180,180','radius'=>'required|integer|min:25|max:50000']);
        $zone = app(OperationZoneService::class)->createZone(['name'=>$this->name,'type'=>$this->type,'geometry_type'=>$this->type === 'support_incident' ? 'point' : 'circle','coordinates'=>['lat'=>$this->latitude,'lng'=>$this->longitude],'radius_meters'=>$this->type === 'support_incident' ? null : $this->radius,'color'=>$this->color($this->type),'note'=>$this->note ?: null], auth()->user());
        $this->selectedZoneId = $zone->id;
        Notification::make()->title('Operation zone created')->success()->send();
    }

    public function updateZone(): void
    {
        $this->validate(['name'=>'required|max:150','radius'=>'required|integer|min:25|max:50000']);
        app(OperationZoneService::class)->updateZone(OperationZone::findOrFail($this->selectedZoneId), ['name'=>$this->name,'radius_meters'=>$this->radius,'note'=>$this->note ?: 'Zone updated from Service Zones.'], auth()->user());
        Notification::make()->title('Operation zone updated')->success()->send();
    }

    public function deactivateZone(): void
    {
        $this->validate(['reason'=>'required|max:2000']);
        app(OperationZoneService::class)->deactivateZone(OperationZone::findOrFail($this->selectedZoneId), auth()->user(), $this->reason);
        Notification::make()->title('Operation zone deactivated')->success()->send();
    }

    public function addNote(): void
    {
        $this->validate(['note'=>'required|max:2000']);
        app(OperationZoneService::class)->addZoneNote(OperationZone::findOrFail($this->selectedZoneId), auth()->user(), $this->note);
        $this->note = '';
        Notification::make()->title('Zone note recorded')->success()->send();
    }

    public function getViewData(): array
    {
        $zones = OperationZone::with(['creator','events.actor'])->latest()->get();
        return ['zones'=>$zones,'selectedZone'=>$zones->firstWhere('id',$this->selectedZoneId),'metrics'=>['total'=>$zones->count(),'active'=>$zones->where('status','active')->count(),'no_go'=>$zones->where('type','no_go_area')->where('status','active')->count(),'events'=>$zones->sum(fn($z)=>$z->events->count())]];
    }

    private function color(string $type): string { return match($type){'no_go_area'=>'#ef4444','priority_area'=>'#f59e0b','service_area'=>'#22d3ee','temporary_busy_area'=>'#a855f7','pickup_hotspot'=>'#10b981',default=>'#f97316'}; }
}
