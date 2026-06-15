<?php
namespace App\Console\Commands;use App\Services\Localization\TranslationManagerService;use Illuminate\Console\Command;
class TranslationsImportCommand extends Command{protected $signature='translations:import';protected $description='Import UTF-8 lang files into audited translation storage.';public function handle(TranslationManagerService $s):int{$this->info($s->importFromLangFiles().' translations imported.');return self::SUCCESS;}}
