<?php

namespace App\Services\Localization;

use App\Models\TranslationEntry;
use App\Models\User;
use Illuminate\Support\Facades\File;

class TranslationManagerService
{
    public function importFromLangFiles(?User $actor = null): int
    {
        $count = 0;

        foreach (config('bikube_locales.supported', []) as $locale => $name) {
            $file = lang_path("{$locale}/bikube.php");

            if (! File::exists($file)) {
                continue;
            }

            foreach ($this->flatten(require $file) as $key => $value) {
                $entry = TranslationEntry::updateOrCreate(
                    ['group' => 'bikube', 'key' => $key, 'locale' => $locale],
                    ['value' => $value, 'status' => 'reviewed'],
                );
                $entry->events()->create([
                    'actor_id' => $actor?->id,
                    'event_type' => 'imported',
                    'description' => 'Imported from UTF-8 lang file.',
                    'created_at' => now(),
                ]);
                $count++;
            }
        }

        return $count;
    }

    public function exportToLangFiles(): int
    {
        $count = 0;

        foreach (array_keys(config('bikube_locales.supported', [])) as $locale) {
            $file = lang_path("{$locale}/bikube.php");
            $catalog = File::exists($file) ? require $file : [];
            $adminUi = [];

            TranslationEntry::query()
                ->where('group', 'admin_ui')
                ->where('locale', $locale)
                ->whereNotNull('value')
                ->orderBy('key')
                ->get()
                ->each(function (TranslationEntry $entry) use (&$adminUi): void {
                    $source = data_get($entry->metadata, 'source');

                    if (is_string($source) && $source !== '' && is_string($entry->value) && $entry->value !== '') {
                        $adminUi[$source] = $entry->value;
                    }
                });

            ksort($adminUi, SORT_NATURAL | SORT_FLAG_CASE);
            $catalog['admin_ui'] = $adminUi;

            File::put($file, "<?php\n\nreturn ".$this->exportArray($catalog).";\n");
            $count++;
        }

        return $count;
    }
    public function getMissingTranslations(): array
    {
        $base = $this->flatten(require lang_path('en/bikube.php'));
        $out = [];

        foreach (array_keys(config('bikube_locales.supported', [])) as $locale) {
            $values = File::exists(lang_path("{$locale}/bikube.php"))
                ? $this->flatten(require lang_path("{$locale}/bikube.php"))
                : [];
            $out[$locale] = array_values(array_diff(array_keys($base), array_keys($values)));
        }

        return $out;
    }

    public function updateTranslation(string $group, string $key, string $locale, string $value, User $actor): TranslationEntry
    {
        $entry = TranslationEntry::updateOrCreate(compact('group', 'key', 'locale'), ['value' => $value, 'status' => 'draft']);
        $entry->events()->create([
            'actor_id' => $actor->id,
            'event_type' => 'updated',
            'description' => 'Translation updated.',
            'created_at' => now(),
        ]);

        return $entry;
    }

    public function approveTranslation(TranslationEntry $entry, User $actor): TranslationEntry
    {
        $entry->update(['status' => 'approved', 'approved_by_id' => $actor->id, 'approved_at' => now()]);
        $entry->events()->create([
            'actor_id' => $actor->id,
            'event_type' => 'approved',
            'description' => 'Translation approved.',
            'created_at' => now(),
        ]);

        return $entry->refresh();
    }

    public function getCoverageReport(): array
    {
        $staticKeys = array_filter(
            array_keys($this->flatten(require lang_path('en/bikube.php'))),
            fn (string $key): bool => ! str_starts_with($key, 'admin_ui.')
        );
        $staticBase = count($staticKeys);
        $staticMissing = collect($this->getMissingTranslations())
            ->map(fn (array $keys): array => array_values(array_filter($keys, fn (string $key): bool => ! str_starts_with($key, 'admin_ui.'))))
            ->all();
        $adminTotal = TranslationEntry::query()
            ->where('group', 'admin_ui')
            ->where('locale', 'en')
            ->whereNotNull('value')
            ->count();

        return collect(array_keys(config('bikube_locales.supported', [])))
            ->map(function (string $locale) use ($staticBase, $staticMissing, $adminTotal): array {
                $adminTranslated = TranslationEntry::query()
                    ->where('group', 'admin_ui')
                    ->where('locale', $locale)
                    ->whereNotNull('value')
                    ->count();
                $missing = count($staticMissing[$locale] ?? []) + max(0, $adminTotal - $adminTranslated);
                $total = $staticBase + $adminTotal;

                return [
                    'locale' => $locale,
                    'total' => $total,
                    'missing' => $missing,
                    'coverage' => $total ? round((($total - $missing) / $total) * 100, 1) : 100,
                ];
            })
            ->values()
            ->all();
    }

    public function scanHardcodedUiStrings(): array
    {
        $hits = [];
        $roots = [
            'app/Filament' => 'medium',
            'resources/views' => 'high',
            'app/Http/Controllers' => 'medium',
            'app/Services' => 'medium',
        ];

        foreach ($roots as $dir => $severity) {
            foreach (File::allFiles(base_path($dir)) as $file) {
                $relative = str_replace(base_path().'/', '', $file->getPathname());
                $uiPath = $this->uiPath($relative);
                $isBlade = str_ends_with($relative, '.blade.php');

                foreach (file($file->getPathname()) ?: [] as $line => $text) {
                    preg_match_all('/["\'][A-Z][^"\']{2,}["\']/', $text, $quotedMatches);
                    foreach ($quotedMatches[0] as $match) {
                        $hits[] = $this->scanHit($uiPath, $relative, $line + 1, $match, $severity);
                    }

                    if ($isBlade) {
                        preg_match_all('/>\s*([^<>{}@][A-Z][^<>{}]{2,}?)\s*</', $text, $textMatches);
                        foreach ($textMatches[1] as $match) {
                            $hits[] = $this->scanHit($uiPath, $relative, $line + 1, $match, $severity);
                        }
                    }
                }
            }
        }

        return $hits;
    }

    private function exportArray(array $catalog): string
    {
        return preg_replace('/=>[ \t]+\n/', "=>\n", var_export($catalog, true)) ?: var_export($catalog, true);
    }
    private function scanHit(string $file, string $path, int $line, string $snippet, string $severity): array
    {
        return [
            'file' => $file,
            'path' => $path,
            'line' => $line,
            'snippet' => $snippet,
            'severity' => $severity,
        ];
    }

    private function uiPath(string $relative): string
    {
        if (str_starts_with($relative, 'app/Filament/')) {
            return substr($relative, strlen('app/Filament/'));
        }

        if (str_starts_with($relative, 'resources/views/')) {
            return substr($relative, strlen('resources/views/'));
        }

        return $relative;
    }

    private function flatten(array $data, string $prefix = ''): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            $nextKey = $prefix === '' ? $key : "{$prefix}.{$key}";

            if (is_array($value)) {
                $out += $this->flatten($value, $nextKey);
            } else {
                $out[$nextKey] = (string) $value;
            }
        }

        return $out;
    }
}