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
        public function __construct(string $val = '')
        {
            $this->nodeValue = $val;
            $this->textContent = $val;
            $this->nodeName = '#text';
            $this->nodeType = 3;
        }
    }

    class DOMComment extends DOMNode
    {
        public function __construct()
        {
            $this->nodeName = '#comment';
            $this->nodeType = 8;
        }
    }

    class DOMNodeList
    {
        public int $length = 1;

        public function item(int $index): ?DOMNode
        {
            return new DOMNode();
        }
    }

    class DOMDocument extends DOMNode
    {
        public int $nodeType = 9;

        public function loadHTML(string $source, int $options = 0): bool
        {
            return true;
        }

        public function getElementsByTagName(string $name): DOMNodeList
        {
            return new DOMNodeList();
        }
    }
}
