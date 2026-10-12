<?php

declare(strict_types=1);

/**
 * Shared Hosting Compatibility & Resilience Layer
 * Provides polyfills for missing PHP extensions on cPanel / shared hosts.
 */

// 1. Polyfill mb_split if mbstring is partially configured
if (!function_exists('mb_split')) {
    function mb_split(string $pattern, string $string, int $limit = -1): array|false
    {
        $delimiter = '/';
        $safePattern = $delimiter . str_replace($delimiter, '\\' . $delimiter, $pattern) . $delimiter . 'u';
        $res = @preg_split($safePattern, $string, $limit);
        return $res !== false ? $res : false;
    }
}

// 2. Polyfill mb_strimwidth for Str::limit()
if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $string, int $start, int $width, string $trim_marker = '', ?string $encoding = null): string
    {
        $hasMbStrlen = function_exists('mb_strlen');
        $hasMbSubstr = function_exists('mb_substr');

        $markerLen = $hasMbStrlen ? mb_strlen($trim_marker, $encoding) : strlen($trim_marker);
        $strLen = $hasMbStrlen ? mb_strlen($string, $encoding) : strlen($string);

        if ($strLen <= $width) {
            return $hasMbSubstr ? (string) mb_substr($string, $start, null, $encoding) : substr($string, $start);
        }

        $targetWidth = max(0, $width - $markerLen);
        $sub = $hasMbSubstr
            ? (string) mb_substr($string, $start, $targetWidth, $encoding)
            : substr($string, $start, $targetWidth);

        return $sub . $trim_marker;
    }
}

// 2. Polyfill DOMDocument and Termwind AST requirements
if (!class_exists('DOMDocument')) {
    if (!defined('LIBXML_NOERROR')) define('LIBXML_NOERROR', 0);
    if (!defined('LIBXML_COMPACT')) define('LIBXML_COMPACT', 0);
    if (!defined('LIBXML_HTML_NODEFDTD')) define('LIBXML_HTML_NODEFDTD', 0);
    if (!defined('LIBXML_NOBLANKS')) define('LIBXML_NOBLANKS', 0);
    if (!defined('LIBXML_NOXMLDECL')) define('LIBXML_NOXMLDECL', 0);

    class DOMNode
    {
        public string $nodeValue = '';
        public string $nodeName = 'body';
        public array $childNodes = [];
        public ?DOMNode $previousSibling = null;
        public ?DOMNode $nextSibling = null;
        public ?DOMNode $parentNode = null;
        public ?DOMNode $firstChild = null;
        public ?DOMNode $lastChild = null;
        public string $textContent = '';
        public int $nodeType = 1;

        public function __construct(string $name = 'body')
        {
            $this->nodeName = $name;
            $this->childNodes = [new DOMText(' ')];
        }

        public function __get(string $name): mixed
        {
            return $this->$name ?? null;
        }

        public function __isset(string $name): bool
        {
            return true;
        }
    }

    class DOMText extends DOMNode
    {
        public function __construct(string $val = ' ')
        {
            $this->nodeValue = $val;
            $this->textContent = $val;
            $this->nodeName = '#text';
            $this->nodeType = 3;
            $this->childNodes = [];
        }
    }

    class DOMComment extends DOMNode
    {
        public function __construct()
        {
            $this->nodeName = '#comment';
            $this->nodeType = 8;
            $this->childNodes = [];
        }
    }

    class DOMNodeList
    {
        public int $length = 1;
        private array $items = [];

        public function __construct(array $items = [])
        {
            if (empty($items)) {
                $node = new DOMNode('body');
                $node->childNodes = [new DOMText(' ')];
                $this->items = [$node];
            } else {
                $this->items = array_values($items);
            }
            $this->length = count($this->items);
        }

        public function item(int $index): ?DOMNode
        {
            return $this->items[$index] ?? ($this->items[0] ?? new DOMNode());
        }
    }

    class DOMDocument extends DOMNode
    {
        public int $nodeType = 9;
        protected string $loadedHtml = '';

        public function loadHTML(string $source, int $options = 0): bool
        {
            $this->loadedHtml = (string) $source;
            return true;
        }

        public function getElementsByTagName(string $name): DOMNodeList
        {
            $node = new DOMNode($name);
            $cleanText = trim(strip_tags($this->loadedHtml));
            $node->childNodes = [new DOMText($cleanText !== '' ? $cleanText : ' ')];
            return new DOMNodeList([$node]);
        }
    }
}
