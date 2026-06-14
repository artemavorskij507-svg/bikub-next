<?php
namespace App\Filament\Resources\WorkerApplications;
use App\Filament\Resources\WorkerApplications\Pages\{EditWorkerApplication,ListWorkerApplications};
use App\Models\WorkerApplication;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\{Select,Textarea,TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class WorkerApplicationResource extends Resource {
 public static function canAccess():bool{return config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')?auth()->check():(auth()->user()?->can('admin.people.view')??false);}
 protected static ?string $model=WorkerApplication::class; protected static string|BackedEnum|null $navigationIcon=Heroicon::OutlinedClipboardDocumentCheck; protected static string|\UnitEnum|null $navigationGroup='People';
 public static function form(Schema $s):Schema{return $s->components([Section::make('Application review')->columns(2)->schema([TextInput::make('name')->disabled(),TextInput::make('email')->disabled(),TextInput::make('phone')->disabled(),TextInput::make('desired_service_area')->disabled(),TextInput::make('vehicle_type')->disabled(),Select::make('status')->options(['submitted'=>'Submitted','needs_user_account'=>'Needs user account','approved'=>'Approved','rejected'=>'Rejected'])->disabled(),Textarea::make('experience_notes')->disabled(),Textarea::make('decision_reason')->disabled()])]);}
 public static function table(Table $t):Table{return $t->persistFiltersInSession()->persistSortInSession()->persistSearchInSession()->persistColumnSearchesInSession()->persistColumnsInSession()->columns([TextColumn::make('name')->searchable(),TextColumn::make('email')->searchable(),TextColumn::make('worker_type')->badge()->formatStateUsing(fn($s)=>str($s)->replace('_',' ')->title()),TextColumn::make('status')->badge()->formatStateUsing(fn($s)=>str($s)->replace('_',' ')->title()),TextColumn::make('user.email')->label('Linked user')->placeholder('No matching user'),TextColumn::make('invitations.status')->label('Invitation')->badge()->limitList(1)->placeholder('None'),TextColumn::make('documents_count')->counts('documents')->label('Documents'),TextColumn::make('submitted_at')->dateTime()])->filters([SelectFilter::make('status')->options(['submitted'=>'Submitted','needs_user_account'=>'Needs user account','approved'=>'Approved','rejected'=>'Rejected'])])->recordActions([EditAction::make()]);}
 public static function getPages():array{return ['index'=>ListWorkerApplications::route('/'),'edit'=>EditWorkerApplication::route('/{record}/edit')];}
}
