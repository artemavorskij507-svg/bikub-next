<?php

namespace App\Services\PublicSite;

class ContentSafetyValidator
{
    private const FAKE_RATING_PATTERN = '/\b[4-5]\.[0-9]\b/';

    private const FAKE_KPI_PATTERNS = [
        '/\b\d{2,}\s*000\s*\+/',   // 10 000+
        '/\b\d{3,}\s*\+/',          // 200+, 1000+
    ];

    private const FAKE_ETA_MINUTES = [
        '20 min', '25 min', '30 min', '35 min', '40 min',
        '20min', '25min', '30min', '35min', '40min',
    ];

    private const FORBIDDEN_HTML = ['<script', 'onclick=', 'onload=', 'onerror=', 'javascript:'];

    public function validate(array $data): array
    {
        $errors = [];

        foreach (['title', 'subtitle', 'body', 'cta_label', 'badge'] as $field) {
            if (empty($data[$field])) {
                continue;
            }

            $texts = is_array($data[$field]) ? array_values($data[$field]) : [$data[$field]];

            foreach ($texts as $text) {
                if (! is_string($text)) {
                    continue;
                }

                if (preg_match(self::FAKE_RATING_PATTERN, $text)) {
                    $errors[$field][] = "Fake rating pattern detected ({$text}). Use 'Rating after launch' safety label instead.";
                }

                foreach (self::FAKE_KPI_PATTERNS as $pattern) {
                    if (preg_match($pattern, $text)) {
                        $errors[$field][] = "Fake KPI count detected ({$text}). Real counts only or omit.";
                        break;
                    }
                }

                foreach (self::FAKE_ETA_MINUTES as $eta) {
                    if (stripos($text, $eta) !== false) {
                        $errors[$field][] = "Fake ETA detected ({$eta}). Use 'ETA after dispatcher confirms' safety label instead.";
                        break;
                    }
                }

                foreach (self::FORBIDDEN_HTML as $forbidden) {
                    if (stripos($text, $forbidden) !== false) {
                        $errors[$field][] = "Forbidden HTML/JS content ({$forbidden}) not allowed in CMS fields.";
                        break;
                    }
                }
            }
        }

        if (! empty($data['badge'])) {
            if (preg_match(self::FAKE_RATING_PATTERN, (string) $data['badge'])) {
                $errors['badge'][] = 'Badge cannot contain rating-like numbers.';
            }
        }

        return $errors;
    }

    public function sanitizeText(?string $text): string
    {
        if ($text === null) {
            return '';
        }

        return strip_tags($text);
    }

    public function sanitizeLocaleField(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_map(fn ($v) => $this->sanitizeText($v), $value);
    }
}
