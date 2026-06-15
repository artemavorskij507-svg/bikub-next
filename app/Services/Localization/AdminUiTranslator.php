<?php

namespace App\Services\Localization;

use App\Models\TranslationEntry;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AdminUiTranslator
{
    public function translateResponse(Response $response): Response
    {
        if (app()->getLocale() === 'en' || ! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $html = $response->getContent();

        if (! is_string($html) || $html === '') {
            return $response;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return $response;
        }

        $translations = trans('bikube.admin_ui');

        if (! is_array($translations)) {
            $translations = [];
        }

        try {
            $translations += TranslationEntry::query()
                ->where('group', 'admin_ui')
                ->where('locale', app()->getLocale())
                ->whereNotNull('value')
                ->get()
                ->mapWithKeys(fn (TranslationEntry $entry): array => [(string) data_get($entry->metadata, 'source') => $entry->value])
                ->filter(fn ($value, $source): bool => is_string($source) && $source !== '' && is_string($value) && $value !== '')
                ->all();
        } catch (Throwable) {
            // The static catalog remains available when the translation database is unavailable.
        }

        $xpath = new DOMXPath($document);

        foreach ($xpath->query('//text()[normalize-space(.) != "" and not(ancestor::script) and not(ancestor::style)]') ?: [] as $node) {
            $node->nodeValue = $this->replaceText($node->nodeValue, $translations);
        }

        foreach ($xpath->query('//*[@title or @aria-label or @placeholder]') ?: [] as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            foreach (['title', 'aria-label', 'placeholder'] as $attribute) {
                if ($node->hasAttribute($attribute)) {
                    $node->setAttribute($attribute, $this->replaceText($node->getAttribute($attribute), $translations));
                }
            }
        }

        $translated = $document->saveHTML();

        if (is_string($translated)) {
            $translated = preg_replace('/^<\?xml encoding="UTF-8"\?>/', '', $translated) ?? $translated;
            $response->setContent(html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        return $response;
    }

    private function replaceText(string $text, array $translations): string
    {
        $trimmed = trim($text);

        if ($trimmed === '' || ! isset($translations[$trimmed])) {
            return $text;
        }

        return str_replace($trimmed, (string) $translations[$trimmed], $text);
    }
}
