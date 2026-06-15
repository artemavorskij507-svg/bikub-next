<?php

namespace App\Console\Commands;

use App\Models\TranslationEntry;
use App\Services\Localization\TranslationManagerService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TranslationsSyncAdminUiCommand extends Command
{
    protected $signature = 'translations:sync-admin-ui {--translate : Create draft machine translations for nb, uk and ru}';

    protected $description = 'Import likely visible Admin OS phrases into Translation Manager.';

    public function handle(TranslationManagerService $manager): int
    {
        $phrases = collect($manager->scanHardcodedUiStrings())
            ->filter(fn (array $hit): bool => $this->isAdminUiFile($hit['file']))
            ->map(fn (array $hit): string => trim($hit['snippet'], "\"'"))
            ->filter(fn (string $phrase): bool => $this->isVisiblePhrase($phrase))
            ->unique()
            ->values();

        foreach ($phrases as $phrase) {
            TranslationEntry::updateOrCreate(
                ['group' => 'admin_ui', 'key' => sha1($phrase), 'locale' => 'en'],
                ['value' => $phrase, 'status' => 'reviewed', 'metadata' => ['source' => $phrase, 'origin' => 'admin_ui_scan']],
            );
        }

        if ($this->option('translate')) {
            foreach (['nb', 'uk', 'ru'] as $locale) {
                $this->translateLocale($phrases->all(), $locale);
            }
        }

        $this->info($phrases->count().' Admin OS phrases synchronized.');
        $this->table(
            ['Locale', 'Status', 'Count'],
            TranslationEntry::query()
                ->where('group', 'admin_ui')
                ->selectRaw('locale, status, count(*) as total')
                ->groupBy('locale', 'status')
                ->orderBy('locale')
                ->orderBy('status')
                ->get()
                ->map(fn (TranslationEntry $entry): array => [$entry->locale, $entry->status, $entry->total])
                ->all(),
        );

        return self::SUCCESS;
    }

    private function translateLocale(array $phrases, string $locale): void
    {
        $completed = TranslationEntry::query()
            ->where('group', 'admin_ui')
            ->where('locale', $locale)
            ->whereNotNull('value')
            ->pluck('key')
            ->all();

        $phrases = array_values(array_filter($phrases, fn (string $phrase): bool => ! in_array(sha1($phrase), $completed, true)));

        foreach (array_chunk($phrases, 20) as $chunk) {
            $responses = Http::pool(fn (Pool $pool): array => array_map(
                fn (string $phrase) => $pool->timeout(20)->get('https://translate.googleapis.com/translate_a/single', [
                    'client' => 'gtx',
                    'sl' => 'en',
                    'tl' => $locale,
                    'dt' => 't',
                    'q' => $phrase,
                ]),
                $chunk,
            ));

            foreach ($chunk as $index => $phrase) {
                $response = $responses[$index] ?? null;
                $translated = $response instanceof Response && $response->successful()
                    ? collect($response->json('0', []))->pluck('0')->implode('')
                    : null;

                TranslationEntry::updateOrCreate(
                    ['group' => 'admin_ui', 'key' => sha1($phrase), 'locale' => $locale],
                    [
                        'value' => filled($translated) ? $translated : null,
                        'status' => filled($translated) ? 'draft' : 'missing',
                        'metadata' => ['source' => $phrase, 'origin' => 'admin_ui_scan', 'translation_method' => 'machine_draft'],
                    ],
                );
            }
        }
    }

    private function isAdminUiFile(string $file): bool
    {
        return str_starts_with($file, 'Pages/')
            || str_starts_with($file, 'Resources/')
            || str_starts_with($file, 'filament/')
            || str_starts_with($file, 'components/admin-os/')
            || str_starts_with($file, 'vendor/filament-locale-switcher/');
    }

    private function isVisiblePhrase(string $phrase): bool
    {
        return Str::length($phrase) >= 3
            && preg_match('/[A-Za-z]{2}/', $phrase) === 1
            && ! str_contains($phrase, '\\')
            && ! str_contains($phrase, '::')
            && ! str_contains($phrase, '->')
            && ! str_contains($phrase, '{$')
            && ! preg_match('/^[a-z0-9_.:-]+$/', $phrase);
    }
}