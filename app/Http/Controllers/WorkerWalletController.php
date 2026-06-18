<?php

namespace App\Http\Controllers;

use App\Services\Finance\WorkerPayoutProfileService;
use App\Services\Finance\WorkerSettlementService;
use Illuminate\Http\Request;

class WorkerWalletController extends Controller
{
    public function index(Request $request)
    {
        $summary = app(WorkerSettlementService::class)->getWorkerEarningsSummary($request->user());

        return view('worker.wallet', [
            'entries'       => $summary['entries']->take(20),
            'readyAmount'   => $summary['ready_amount'],
            'paidAmount'    => $summary['paid_amount'],
            'blockedCount'  => $summary['blocked_count'],
            'payoutProfile' => app(WorkerPayoutProfileService::class)->getReadiness($request->user()),
        ]);
    }
}
