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

// 3. Polyfill finfo and FileInfo functions/constants for shared hosting environments where ext-fileinfo is disabled
if (!defined('FILEINFO_NONE')) {
    define('FILEINFO_NONE', 0);
}
if (!defined('FILEINFO_SYMLINK')) {
    define('FILEINFO_SYMLINK', 2);
}
if (!defined('FILEINFO_MIME')) {
    define('FILEINFO_MIME', 1040);
}
if (!defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (!defined('FILEINFO_MIME_ENCODING')) {
    define('FILEINFO_MIME_ENCODING', 1024);
}
if (!defined('FILEINFO_DEVICES')) {
    define('FILEINFO_DEVICES', 8);
}
if (!defined('FILEINFO_CONTINUE')) {
    define('FILEINFO_CONTINUE', 32);
}
if (!defined('FILEINFO_PRESERVE_ATIME')) {
    define('FILEINFO_PRESERVE_ATIME', 128);
}
if (!defined('FILEINFO_RAW')) {
    define('FILEINFO_RAW', 256);
}

if (!class_exists('finfo', false)) {
    class finfo
    {
        protected int $flags;
        protected ?string $magicFile;

        public function __construct(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null)
        {
            $this->flags = $flags;
            $this->magicFile = $magicFile;
        }

        public function set_flags(int $flags): bool
        {
            $this->flags = $flags;
            return true;
        }

        public function file(string $filename, int $flags = FILEINFO_NONE, $context = null): string|false
        {
            if (!file_exists($filename) || !is_readable($filename)) {
                return false;
            }

            // Try native mime_content_type if available
            if (function_exists('mime_content_type')) {
                $nativeMime = @mime_content_type($filename);
                if (!empty($nativeMime)) {
                    return $nativeMime;
                }
            }

            // Read first few bytes for signature detection
            $handle = @fopen($filename, 'rb');
            if ($handle) {
                $sample = (string) @fread($handle, 1024);
                @fclose($handle);
                $byMagic = self::guessFromMagicBytes($sample);
                if ($byMagic) {
                    return $byMagic;
                }
            }

            // Fallback to extension lookup
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            return self::guessFromExtension($ext) ?? 'application/octet-stream';
        }

        public function buffer(string $string, int $flags = FILEINFO_NONE, $context = null): string|false
        {
            $byMagic = self::guessFromMagicBytes($string);
            if ($byMagic) {
                return $byMagic;
            }

            return 'application/octet-stream';
        }

        protected static function guessFromMagicBytes(string $sample): ?string
        {
            if (strlen($sample) === 0) {
                return 'application/x-empty';
            }

            if (str_starts_with($sample, "\xFF\xD8\xFF")) {
                return 'image/jpeg';
            }
            if (str_starts_with($sample, "\x89PNG\x0D\x0A\x1A\x0A")) {
                return 'image/png';
            }
            if (str_starts_with($sample, "GIF87a") || str_starts_with($sample, "GIF89a")) {
                return 'image/gif';
            }
            if (str_starts_with($sample, "RIFF") && substr($sample, 8, 4) === "WEBP") {
                return 'image/webp';
            }
            if (str_starts_with($sample, "%PDF-")) {
                return 'application/pdf';
            }
            if (str_starts_with($sample, "PK\x03\x04")) {
                return 'application/zip';
            }
            if (str_starts_with($sample, "\x1A\x45\xDF\xA3")) {
                return 'video/webm';
            }
            if (strlen($sample) >= 8 && substr($sample, 4, 4) === 'ftyp') {
                return 'video/mp4';
            }
            if (stripos($sample, '<svg') !== false) {
                return 'image/svg+xml';
            }
            if (stripos($sample, '<html') !== false || stripos($sample, '<!doctype html') !== false) {
                return 'text/html';
            }

            return null;
        }

        public static function guessFromExtension(string $extension): ?string
        {
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'bmp' => 'image/bmp',
                'ico' => 'image/x-icon',
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'txt' => 'text/plain',
                'csv' => 'text/csv',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'mp4' => 'video/mp4',
                'webm' => 'video/webm',
                'mov' => 'video/quicktime',
                'qt' => 'video/quicktime',
                'ogg' => 'video/ogg',
                'mp3' => 'audio/mpeg',
                'wav' => 'audio/wav',
            ];

            return $map[$extension] ?? null;
        }
    }
}

if (!function_exists('finfo_open')) {
    function finfo_open(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null): finfo|false
    {
        return new finfo($flags, $magicFile);
    }
}

if (!function_exists('finfo_close')) {
    function finfo_close(finfo $finfo): bool
    {
        return true;
    }
}

if (!function_exists('finfo_file')) {
    function finfo_file(finfo $finfo, string $filename, int $flags = FILEINFO_NONE, $context = null): string|false
    {
        return $finfo->file($filename, $flags, $context);
    }
}

if (!function_exists('finfo_buffer')) {
    function finfo_buffer(finfo $finfo, string $string, int $flags = FILEINFO_NONE, $context = null): string|false
    {
        return $finfo->buffer($string, $flags, $context);
    }
}

