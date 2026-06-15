<?php

namespace App\Services\Finance;

use App\Models\{Order, User, WorkerSettlementRule};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkerSettlementRuleService
{
    public function __construct(private WorkerSettlementRuleReviewService $reviews) {}
    public function createDraft(array $data, User $actor): WorkerSettlementRule
    {
        return DB::transaction(function () use ($data, $actor) {
            $data = $this->validateRule($data);
            $rule = WorkerSettlementRule::create($data + ['rule_number' => $this->generateNumber(), 'status' => 'draft', 'created_by_id' => $actor->id, 'updated_by_id' => $actor->id]);
            $this->event($rule, $actor, 'created', null, 'draft', 'Settlement rule draft created. No legal, tax, or payout approval is implied.');
            return $rule;
        });
    }

    public function updateDraft(WorkerSettlementRule $rule, array $data, User $actor): WorkerSettlementRule
    {
        throw_unless($rule->status === 'draft', ValidationException::withMessages(['rule' => 'Only draft settlement rules can be edited.']));
        $rule->update($this->validateRule($data) + ['updated_by_id' => $actor->id]);
        $this->event($rule, $actor, 'updated', 'draft', 'draft', 'Settlement rule draft updated.');
        return $rule->refresh();
    }

    public function approve(WorkerSettlementRule $rule, User $actor, string $note): WorkerSettlementRule
    {
        throw_unless($actor->can('admin.settlement_rules.activate'), ValidationException::withMessages(['actor' => 'Settlement rule activation permission is required.']));
        throw_if(blank($note), ValidationException::withMessages(['note' => 'Approval note is required.']));
        $reviewReadiness = $this->reviews->getRuleReviewReadiness($rule);
        throw_unless($reviewReadiness['ready'], ValidationException::withMessages(['review' => $reviewReadiness['blockers']]));
        $this->validateRule($rule->toArray());
        $from = $rule->status;
        $rule->update(['status' => 'active', 'approved_by_id' => $actor->id, 'approved_at' => now(), 'approval_note' => $note, 'updated_by_id' => $actor->id]);
        $this->event($rule, $actor, 'approved', $from, 'active', $note);
        return $rule->refresh();
    }

    public function reject(WorkerSettlementRule $rule, User $actor, string $reason): WorkerSettlementRule { return $this->transition($rule, $actor, 'rejected', 'rejected', $reason); }
    public function archive(WorkerSettlementRule $rule, User $actor, string $reason): WorkerSettlementRule { return $this->transition($rule, $actor, 'archived', 'archived', $reason); }

    public function findApplicableRule(Order $order, User $worker): ?WorkerSettlementRule
    {
        $gross = (float) ($order->billingDocuments()->where('status', 'issued')->latest()->value('total_amount') ?? 0);
        return WorkerSettlementRule::query()->where('status', 'active')->where('legal_review_status', 'approved')->where('tax_review_status', 'approved')
            ->where(fn ($q) => $q->whereNull('service_scenario_key')->orWhere('service_scenario_key', $order->service_scenario_key))
            ->where(fn ($q) => $q->whereNull('worker_role')->orWhere('worker_role', 'worker'))
            ->where(fn ($q) => $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', today()))
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', today()))
            ->where(fn ($q) => $q->whereNull('min_order_amount')->orWhere('min_order_amount', '<=', $gross))
            ->where(fn ($q) => $q->whereNull('max_order_amount')->orWhere('max_order_amount', '>=', $gross))
            ->latest('approved_at')->first();
    }

    public function calculateAmounts(Order $order, WorkerSettlementRule $rule): array
    {
        $gross = (float) $order->billingDocuments()->where('status', 'issued')->latest()->value('total_amount');
        throw_if($gross <= 0, ValidationException::withMessages(['invoice' => 'Issued invoice total is required.']));
        throw_unless($rule->status === 'active' && $rule->legal_review_status === 'approved' && $rule->tax_review_status === 'approved', ValidationException::withMessages(['rule' => 'Only an active legal/tax-approved settlement rule can calculate amounts.']));
        throw_if($rule->calculation_type === 'manual_review', ValidationException::withMessages(['rule' => 'Settlement rule requires manual review; amounts cannot be calculated automatically.']));
        $worker = $rule->calculation_type === 'fixed_amount' ? (float) $rule->fixed_worker_amount : round($gross * (float) $rule->worker_share_percent / 100, 2);
        $platform = $rule->calculation_type === 'fixed_amount' ? null : round($gross * (float) $rule->platform_fee_percent / 100, 2);
        return ['gross_amount' => $gross, 'worker_amount' => $worker, 'platform_fee_amount' => $platform, 'currency' => $rule->currency, 'calculation_basis' => $rule->rule_number.' — '.$rule->name];
    }

    public function getRuleBlockers(Order $order, User $worker): array
    {
        if ($this->findApplicableRule($order, $worker)) return [];
        $candidate = WorkerSettlementRule::where(fn ($q) => $q->whereNull('service_scenario_key')->orWhere('service_scenario_key', $order->service_scenario_key))->latest()->first();
        if ($candidate && ($candidate->legal_review_status !== 'approved' || $candidate->tax_review_status !== 'approved')) return ['Worker settlement rule requires legal/tax approval.'];
        return ['Worker settlement rule is not configured.'];
    }

    private function validateRule(array $data): array
    {
        $type = $data['calculation_type'] ?? null;
        throw_unless(in_array($type, ['percent_split', 'fixed_amount', 'manual_review'], true), ValidationException::withMessages(['calculation_type' => 'Valid calculation type is required.']));
        if ($type === 'percent_split') {
            $total = (float) ($data['worker_share_percent'] ?? 0) + (float) ($data['platform_fee_percent'] ?? 0);
            throw_unless(abs($total - 100) < 0.001, ValidationException::withMessages(['worker_share_percent' => 'Worker share and platform fee must total 100%.']));
        }
        if ($type === 'fixed_amount') throw_if(! isset($data['fixed_worker_amount']) || (float) $data['fixed_worker_amount'] < 0, ValidationException::withMessages(['fixed_worker_amount' => 'Fixed worker amount is required.']));
        return collect($data)->only((new WorkerSettlementRule)->getFillable())->except(['rule_number', 'status', 'approved_by_id', 'approved_at', 'created_by_id', 'updated_by_id'])->all();
    }

    private function transition(WorkerSettlementRule $rule, User $actor, string $status, string $event, string $reason): WorkerSettlementRule
    {
        throw_unless($actor->can('admin.settlement_rules.manage'), ValidationException::withMessages(['actor' => 'Settlement rule management permission is required.']));
        throw_if(blank($reason), ValidationException::withMessages(['reason' => ucfirst($event).' reason is required.']));
        $from = $rule->status;
        $rule->update(['status' => $status, 'rejected_by_id' => $status === 'rejected' ? $actor->id : $rule->rejected_by_id, 'rejected_at' => $status === 'rejected' ? now() : $rule->rejected_at, 'updated_by_id' => $actor->id]);
        $this->event($rule, $actor, $event, $from, $status, $reason);
        return $rule->refresh();
    }

    private function event(WorkerSettlementRule $rule, User $actor, string $type, ?string $from, ?string $to, string $description): void { $rule->events()->create(['actor_id' => $actor->id, 'event_type' => $type, 'from_value' => $from, 'to_value' => $to, 'description' => $description, 'created_at' => now()]); }
    private function generateNumber(): string { do { $number = 'WSR-'.now()->format('Ymd').'-'.strtoupper(str()->random(6)); } while (WorkerSettlementRule::where('rule_number', $number)->exists()); return $number; }
}
