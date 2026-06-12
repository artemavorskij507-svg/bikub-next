<?php

namespace App\Services\Workers;

use App\Models\{User, WorkerDocument};
use Illuminate\Validation\ValidationException;

class WorkerDocumentComplianceService
{
    public function evaluate(WorkerDocument $document): array
    {
        $expired = $document->expires_at?->isPast() ?? false;
        $hasEvidence = $document->hasReviewableFile();
        $manual = $document->manually_verified && filled($document->verification_note);
        $status = match (true) {
            $expired => 'expired',
            $document->status === 'rejected' => 'rejected',
            $document->status === 'approved' => 'approved',
            $manual => 'manually_verified',
            $hasEvidence => 'evidence_uploaded',
            $document->required => 'missing_evidence',
            default => 'pending',
        };

        return ['status' => $status, 'risk' => in_array($status, ['expired', 'rejected', 'missing_evidence'], true) ? 'high' : ($status === 'approved' ? 'low' : 'medium')];
    }

    public function canApprove(WorkerDocument $document): bool
    {
        return ! ($document->expires_at?->isPast() ?? false)
            && ($document->hasReviewableFile() || ($document->manually_verified && filled($document->verification_note)));
    }

    public function approve(WorkerDocument $document, User $reviewer, string $note): WorkerDocument
    {
        if (blank($note) || ! $this->canApprove($document)) {
            throw ValidationException::withMessages(['document' => 'Approval requires non-expired evidence or manual verification, plus a review note.']);
        }
        $document->update(['status' => 'approved', 'compliance_status' => 'approved', 'risk_level' => 'low', 'verification_note' => $note, 'reviewed_at' => now(), 'approved_at' => now(), 'rejected_at' => null, 'reviewed_by_user_id' => $reviewer->id]);
        activity()->causedBy($reviewer)->performedOn($document)->withProperties(['document_type' => $document->document_type, 'note' => $note])->event('worker_document.approved')->log('Worker document approved');
        return $document->refresh();
    }

    public function reject(WorkerDocument $document, User $reviewer, string $reason): WorkerDocument
    {
        if (blank($reason)) throw ValidationException::withMessages(['reason' => 'Rejection reason is required.']);
        $document->update(['status' => 'rejected', 'compliance_status' => 'rejected', 'risk_level' => 'high', 'rejection_reason' => $reason, 'reviewed_at' => now(), 'rejected_at' => now(), 'approved_at' => null, 'reviewed_by_user_id' => $reviewer->id]);
        activity()->causedBy($reviewer)->performedOn($document)->withProperties(['document_type' => $document->document_type, 'reason' => $reason])->event('worker_document.rejected')->log('Worker document rejected');
        return $document->refresh();
    }

    public function markExpiredIfNeeded(WorkerDocument $document): WorkerDocument
    {
        if ($document->expires_at?->isPast()) $document->update(['status' => 'expired', 'compliance_status' => 'expired', 'risk_level' => 'high']);
        return $document->refresh();
    }
}
