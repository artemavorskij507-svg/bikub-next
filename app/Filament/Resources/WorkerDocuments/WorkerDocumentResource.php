<?php
namespace App\Filament\Resources\WorkerDocuments;
use App\Filament\Resources\WorkerDocuments\Pages\{EditWorkerDocument,ListWorkerDocuments};
use App\Models\WorkerDocument;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\{Checkbox,Select,Textarea,TextInput};
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\{IconColumn,TextColumn};
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
class WorkerDocumentResource extends Resource {
 public static function canAccess():bool{return config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')?auth()->check():(auth()->user()?->can('admin.people.view')??false);}
 protected static ?string $model=WorkerDocument::class; protected static string|BackedEnum|null $navigationIcon=Heroicon::OutlinedDocumentCheck; protected static string|\UnitEnum|null $navigationGroup='People';
 public static function form(Schema $s):Schema{return $s->components([Section::make('Document review')->columns(2)->schema([Select::make('worker_application_id')->relationship('application','email')->searchable(),Select::make('document_type')->options(array_combine($d=['identity','work_permission','driver_license','vehicle_registration','insurance','tax_information','other'],$d))->required(),Checkbox::make('required'),SpatieMediaLibraryFileUpload::make('document_upload')->collection('worker_documents')->disk('local')->visibility('private')->acceptedFileTypes(['application/pdf','image/jpeg','image/png','image/webp'])->maxSize(10240)->downloadable()->helperText('Private admin-only evidence. Upload does not approve the document.'),Checkbox::make('manually_verified'),Textarea::make('verification_note'),Select::make('status')->options(['pending'=>'Pending','submitted'=>'Submitted','approved'=>'Approved','rejected'=>'Rejected','expired'=>'Expired'])->required(),Textarea::make('rejection_reason')])]);}
 public static function table(Table $t):Table{return $t->columns([TextColumn::make('application.email')->label('Applicant'),TextColumn::make('document_type')->badge()->formatStateUsing(fn($s)=>str($s)->replace('_',' ')->title()),TextColumn::make('status')->badge()->formatStateUsing(fn($s)=>str($s)->replace('_',' ')->title()),IconColumn::make('required')->boolean(),IconColumn::make('manually_verified')->boolean(),IconColumn::make('has_media')->label('Uploaded')->state(fn(WorkerDocument $record)=>$record->hasMedia('worker_documents'))->boolean()])->filters([SelectFilter::make('status')->options(['pending'=>'Pending','submitted'=>'Submitted','approved'=>'Approved','rejected'=>'Rejected','expired'=>'Expired'])])->recordActions([EditAction::make()]);}
 public static function getPages():array{return ['index'=>ListWorkerDocuments::route('/'),'edit'=>EditWorkerDocument::route('/{record}/edit')];}
}
