<?php
namespace App\Console\Commands;use App\Services\Security\SecurityGovernanceSlaService;use Illuminate\Console\Command;
class GovernanceSlaCheckCommand extends Command{protected $signature='security:governance-sla-check {--dry-run} {--notify} {--only-critical}';protected $description='Evaluate database governance notification SLA queues';public function handle(SecurityGovernanceSlaService $s):int{$r=$s->runSlaCheck(null,(bool)$this->option('dry-run'));$this->info(json_encode($r).' No external email or SMS was sent.');return self::SUCCESS;}}
