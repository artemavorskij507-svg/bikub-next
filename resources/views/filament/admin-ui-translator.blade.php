@php
    $adminUiTranslations = app(\App\Services\Localization\AdminUiTranslator::class)->translations();
@endphp

@if (app()->getLocale() !== 'en' && $adminUiTranslations !== [])
    <script>
        (() => {
            const translations = @json($adminUiTranslations);
            const attributes = ['title', 'aria-label', 'placeholder'];
            const ignored = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'OPTION']);

            const translateText = (value) => {
                const trimmed = value.trim();
                return trimmed && translations[trimmed]
                    ? value.replace(trimmed, translations[trimmed])
                    : value;
            };

            const translateNode = (root) => {
                if (!root || root.nodeType !== Node.ELEMENT_NODE || ignored.has(root.tagName)) return;

                const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
                let node;
                while ((node = walker.nextNode())) {
                    if (!ignored.has(node.parentElement?.tagName)) node.nodeValue = translateText(node.nodeValue);
                }

                [root, ...root.querySelectorAll('*')].forEach((element) => {
                    attributes.forEach((attribute) => {
                        if (element.hasAttribute?.(attribute)) {
                            element.setAttribute(attribute, translateText(element.getAttribute(attribute)));
                        }
                    });
                });
            };

            const start = () => {
                translateNode(document.body);
                new MutationObserver((mutations) => mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.TEXT_NODE) {
                            if (!ignored.has(node.parentElement?.tagName)) node.nodeValue = translateText(node.nodeValue);
                        } else {
                            translateNode(node);
                        }
                    });
                })).observe(document.body, { childList: true, subtree: true });
            };

            document.readyState === 'loading'
                ? document.addEventListener('DOMContentLoaded', start, { once: true })
                : start();
        })();
    </script>
@endif
