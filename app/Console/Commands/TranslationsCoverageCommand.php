<?php
namespace App\Console\Commands;use App\Services\Localization\TranslationManagerService;use Illuminate\Console\Command;
class TranslationsCoverageCommand extends Command{protected $signature='translations:coverage';protected $description='Report BiKuBe translation coverage.';public function handle(TranslationManagerService $s):int{$this->table(['Locale','Total','Missing','Coverage'],array_map(fn($r)=>[$r['locale'],$r['total'],$r['missing'],$r['coverage'].'%'],$s->getCoverageReport()));return self::SUCCESS;}}
