<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

final class HtmlSanitizerService
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'code', 'div', 'em', 'h1', 'h2', 'h3',
        'h4', 'h5', 'h6', 'i', 'iframe', 'img', 'li', 'ol', 'p', 'pre',
        's', 'span', 'strong', 'u', 'ul',
    ];

    private const DROP_WITH_CONTENT = ['script', 'style', 'svg', 'math', 'object', 'embed', 'template'];

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div data-sanitizer-root="1">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $root = $document->getElementsByTagName('div')->item(0);
        if (! $root instanceof DOMElement) {
            return '';
        }

        self::sanitizeChildren($root);

        $clean = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $clean .= $document->saveHTML($child);
        }

        return $clean;
    }

    private static function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $node) {
            if ($node->nodeType === XML_COMMENT_NODE) {
                $parent->removeChild($node);

                continue;
            }

            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                    $parent->removeChild($node);

                    continue;
                }

                self::sanitizeChildren($node);
                while ($node->firstChild) {
                    $parent->insertBefore($node->firstChild, $node);
                }
                $parent->removeChild($node);

                continue;
            }

            self::sanitizeAttributes($node, $tag);
            self::sanitizeChildren($node);
        }
    }

    private static function sanitizeAttributes(DOMElement $element, string $tag): void
    {
        $allowed = match ($tag) {
            'a' => ['href', 'target', 'class'],
            'img' => ['src', 'alt', 'width', 'height', 'style'],
            'iframe' => ['src', 'width', 'height', 'allowfullscreen'],
            'div', 'span' => ['class', 'dir'],
            default => ['dir'],
        };

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            if (! in_array($name, $allowed, true)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($element->hasAttribute('class')) {
            $allowedClasses = array_intersect(
                preg_split('/\s+/', trim($element->getAttribute('class'))) ?: [],
                ['pdf-attachment-badge', 'video-responsive-container']
            );
            if ($allowedClasses === []) {
                $element->removeAttribute('class');
            } else {
                $element->setAttribute('class', implode(' ', $allowedClasses));
            }
        }

        if ($tag === 'img') {
            if (! self::isAllowedMediaUrl($element->getAttribute('src'))) {
                $element->removeAttribute('src');
            }
            if ($element->hasAttribute('style')) {
                $style = self::sanitizeImageStyle($element->getAttribute('style'));
                $style === '' ? $element->removeAttribute('style') : $element->setAttribute('style', $style);
            }
        }

        if ($tag === 'a') {
            if (! self::isSafeHttpUrl($element->getAttribute('href'))) {
                $element->removeAttribute('href');
                $element->removeAttribute('target');
            } elseif ($element->getAttribute('target') === '_blank') {
                $element->setAttribute('rel', 'noopener noreferrer');
            } else {
                $element->removeAttribute('target');
            }
        }

        if ($tag === 'iframe') {
            if (! self::isAllowedYoutubeEmbed($element->getAttribute('src'))) {
                $element->parentNode?->removeChild($element);

                return;
            }
            $element->setAttribute('sandbox', 'allow-scripts allow-same-origin allow-presentation');
            $element->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        }
    }

    private static function sanitizeImageStyle(string $style): string
    {
        $safe = [];
        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }
            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            $value = strtolower($value);

            $allowedValue = match ($property) {
                'width', 'max-width', 'height' => preg_match('/\A(?:auto|\d+(?:\.\d+)?(?:px|%))\z/', $value),
                'border-radius' => preg_match('/\A\d+(?:\.\d+)?px\z/', $value),
                'margin' => preg_match('/\A(?:auto|0|\d+(?:\.\d+)?(?:px|rem|%))(?:\s+(?:auto|0|\d+(?:\.\d+)?(?:px|rem|%))){0,3}\z/', $value),
                default => false,
            };

            if ($allowedValue) {
                $safe[] = $property.': '.$value;
            }
        }

        return implode('; ', $safe);
    }

    private static function isSafeHttpUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/storage/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    private static function isAllowedMediaUrl(string $url): bool
    {
        if (! self::isSafeHttpUrl($url)) {
            return false;
        }

        if (str_starts_with($url, '/storage/')) {
            return true;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowedHosts = collect([
            config('app.url'),
            config('filesystems.disks.public.url'),
            config('filesystems.disks.s3.url'),
            config('filesystems.disks.s3.endpoint'),
        ])->filter()->map(fn (string $configuredUrl) => strtolower((string) parse_url($configuredUrl, PHP_URL_HOST)));

        return $host !== '' && $allowedHosts->contains($host);
    }

    private static function isAllowedYoutubeEmbed(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);

        return $scheme === 'https'
            && in_array($host, ['www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'youtube-nocookie.com'], true)
            && preg_match('#\A/embed/[A-Za-z0-9_-]{11}\z#', ltrim($path, '/')) === 1;
    }
}
