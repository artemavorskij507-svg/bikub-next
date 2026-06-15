<?php

namespace App\Filament\Resources\WorkerSettlementRules;

use App\Filament\Resources\WorkerSettlementRules\Pages\{CreateWorkerSettlementRule, EditWorkerSettlementRule, ListWorkerSettlementRules};
use App\Models\WorkerSettlementRule;
use App\Services\Finance\WorkerSettlementRuleService;
use App\Services\Finance\WorkerSettlementRuleReviewService;
use Filament\Actions\{Action, EditAction};
use Filament\Forms\Components\{DatePicker, Select, Textarea, TextInput};
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkerSettlementRuleResource extends Resource
{
    protected static ?string $model = WorkerSettlementRule::class;
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?string $navigationLabel = 'Worker Settlement Rules';
    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool { return auth()->user()?->can('admin.settlement_rules.view') || auth()->user()?->can('admin.settlement_reviews.view') || false; }
    public static function canCreate(): bool { return auth()->user()?->can('admin.settlement_rules.manage') ?? false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Audited settlement rule')->description('Draft configuration does not imply legal, tax, payment, or payout approval.')->columns(2)->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('rule_number')->disabled()->dehydrated(false),
            TextInput::make('service_scenario_key')->maxLength(255),
            TextInput::make('worker_role')->maxLength(255),
            Select::make('calculation_type')->options(['percent_split' => 'Percent split', 'fixed_amount' => 'Fixed worker amount', 'manual_review' => 'Manual review'])->required()->live(),
            TextInput::make('currency')->default('NOK')->required()->maxLength(3),
            TextInput::make('worker_share_percent')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('platform_fee_percent')->numeric()->minValue(0)->maxValue(100),
            TextInput::make('fixed_worker_amount')->numeric()->minValue(0),
            TextInput::make('min_order_amount')->numeric()->minValue(0),
            TextInput::make('max_order_amount')->numeric()->minValue(0),
            Select::make('legal_review_status')->options(['required' => 'Required', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])->default('required')->required(),
            Select::make('tax_review_status')->options(['required' => 'Required', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])->default('required')->required(),
            DatePicker::make('effective_from'), DatePicker::make('effective_until'),
            Textarea::make('approval_note')->disabled()->columnSpanFull(),
        ])]);
    }

    public static function table(Table $table): Table
    {
        return $table->persistFiltersInSession()->persistSortInSession()->persistSearchInSession()->persistColumnsInSession()->columns([
            TextColumn::make('rule_number')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('calculation_type')->badge(), TextColumn::make('worker_share_percent')->suffix('%')->placeholder('Not applicable'), TextColumn::make('platform_fee_percent')->suffix('%')->placeholder('Not applicable'), TextColumn::make('legal_review_status')->badge(), TextColumn::make('tax_review_status')->badge(), TextColumn::make('effective_from')->date(), TextColumn::make('approved_at')->dateTime()->placeholder('Not approved'),
        ])->filters([SelectFilter::make('status')->options(['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived', 'rejected' => 'Rejected'])])->recordActions([
            EditAction::make()->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.settlement_rules.manage') ?? false)),
            Action::make('approve')->color('success')->requiresConfirmation()->form([Textarea::make('note')->required()->helperText('Approval is blocked until legal, tax, and finance reviews are explicitly approved.')])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleService::class)->approve($record, auth()->user(), $data['note']))->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.settlement_rules.activate') ?? false)),
            Action::make('requestLegalReview')->label('Request legal review')->form([Textarea::make('note')])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleReviewService::class)->requestReview($record, 'legal', auth()->user(), $data['note'] ?? null))->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.finance.manage') ?? false)),
            Action::make('requestTaxReview')->label('Request tax review')->form([Textarea::make('note')])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleReviewService::class)->requestReview($record, 'tax', auth()->user(), $data['note'] ?? null))->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.finance.manage') ?? false)),
            Action::make('requestFinanceReview')->label('Request finance review')->form([Textarea::make('note')])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleReviewService::class)->requestReview($record, 'finance', auth()->user(), $data['note'] ?? null))->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.finance.manage') ?? false)),
            Action::make('reject')->color('danger')->requiresConfirmation()->form([Textarea::make('reason')->required()])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleService::class)->reject($record, auth()->user(), $data['reason']))->visible(fn (WorkerSettlementRule $record) => $record->status === 'draft' && (auth()->user()?->can('admin.finance.manage') ?? false)),
            Action::make('archive')->color('warning')->requiresConfirmation()->form([Textarea::make('reason')->required()])->action(fn (WorkerSettlementRule $record, array $data) => app(WorkerSettlementRuleService::class)->archive($record, auth()->user(), $data['reason']))->visible(fn (WorkerSettlementRule $record) => $record->status === 'active' && (auth()->user()?->can('admin.settlement_rules.archive') ?? false)),
        ]);
    }

    public static function getPages(): array { return ['index' => ListWorkerSettlementRules::route('/'), 'create' => CreateWorkerSettlementRule::route('/create'), 'edit' => EditWorkerSettlementRule::route('/{record}/edit')]; }
}
