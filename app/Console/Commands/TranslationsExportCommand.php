<?php

namespace App\Console\Commands;

use App\Services\Localization\TranslationManagerService;
use Illuminate\Console\Command;

class TranslationsExportCommand extends Command
{
    protected $signature = 'translations:export';

    protected $description = 'Export audited translations to UTF-8 lang files.';

    public function handle(TranslationManagerService $service): int
    {
        $this->info($service->exportToLangFiles().' translation files exported.');

        return self::SUCCESS;
    }
}