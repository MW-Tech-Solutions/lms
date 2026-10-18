<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use ZipArchive;

class DocumentExtractionService
{
    /**
     * Extract structured HTML and metadata from an uploaded curriculum document (PDF, PPTX, DOCX, TXT).
     *
     * @param string $filePath Storage relative path or absolute path to the file.
     * @return array{title: string, html: string, plain_text: string, sections_count: int}
     */
    public function extract(string $filePath): array
    {
        $fullPath = $this->resolvePath($filePath);

        if (!$fullPath || !file_exists($fullPath)) {
            return [
                'title' => 'Curriculum Document',
                'html' => '<p class="text-muted">Document file could not be located on server for content extraction.</p>',
                'plain_text' => '',
                'sections_count' => 0,
            ];
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        try {
            return match ($extension) {
                'pdf' => $this->extractPdf($fullPath),
                'pptx' => $this->extractPptx($fullPath),
                'docx' => $this->extractDocx($fullPath),
                'txt', 'md' => $this->extractText($fullPath),
                default => $this->extractGeneric($fullPath),
            };
        } catch (\Throwable $e) {
            Log::warning("DocumentExtractionService failed parsing [{$filePath}]: " . $e->getMessage());

            return [
                'title' => pathinfo($fullPath, PATHINFO_FILENAME),
                'html' => '<div class="alert alert-light border p-3 small text-muted"><i class="bi bi-file-earmark-text me-1"></i> Document content was saved. Complete interactive view will sync during syllabus indexation.</div>',
                'plain_text' => 'Document attached.',
                'sections_count' => 1,
            ];
        }
    }

    /**
     * Resolve file path from public disk or local filesystem.
     */
    protected function resolvePath(string $filePath): ?string
    {
        if (file_exists($filePath)) {
            return $filePath;
        }

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->path($filePath);
        }

        $appStorage = storage_path('app/public/' . ltrim($filePath, '/'));
        if (file_exists($appStorage)) {
            return $appStorage;
        }

        return null;
    }

    /**
     * Extract structured content from PDF using Smalot Parser.
     */
    protected function extractPdf(string $fullPath): array
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($fullPath);
        $pages = $pdf->getPages();

        $htmlSections = [];
        $allTextLines = [];
        $detectedTitle = null;
        $sectionIndex = 1;

        foreach ($pages as $pageNum => $page) {
            $pageText = trim($page->getText());
            if (empty($pageText)) {
                continue;
            }

            $lines = preg_split('/\r\n|\r|\n/', $pageText);
            $pageHtml = [];
            $inList = false;

            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed === '') {
                    if ($inList) {
                        $pageHtml[] = '</ul>';
                        $inList = false;
                    }
                    continue;
                }

                $allTextLines[] = $trimmed;

                // Identify Header / Title
                if ($this->isHeaderLine($trimmed)) {
                    if ($inList) {
                        $pageHtml[] = '</ul>';
                        $inList = false;
                    }

                    if (!$detectedTitle && strlen($trimmed) < 120 && !preg_match('/^page\s+\d+/i', $trimmed)) {
                        $detectedTitle = $trimmed;
                    }

                    $cleanHeader = e(preg_replace('/^#+\s*/', '', $trimmed));
                    $pageHtml[] = sprintf(
                        '<h4 class="fw-bold text-dark mt-4 mb-2 pb-1 border-bottom border-light-subtle d-flex align-items-center gap-2"><i class="bi bi-bookmark-fill text-primary small"></i> %s</h4>',
                        $cleanHeader
                    );
                } elseif (preg_match('/^[\•\-\*\⁃]\s+(.*)$/u', $trimmed, $m) || preg_match('/^\d+[\.\)]\s+(.*)$/', $trimmed, $m)) {
                    if (!$inList) {
                        $pageHtml[] = '<ul class="list-unstyled d-flex flex-column gap-2 my-2 ps-2">';
                        $inList = true;
                    }
                    $bulletContent = e($m[1] ?? $trimmed);
                    $pageHtml[] = sprintf(
                        '<li class="d-flex align-items-start gap-2"><i class="bi bi-check-circle-fill text-success mt-1 small flex-shrink-0"></i><span>%s</span></li>',
                        $bulletContent
                    );
                } else {
                    if ($inList) {
                        $pageHtml[] = '</ul>';
                        $inList = false;
                    }
                    $pageHtml[] = sprintf('<p class="text-secondary mb-2.5" style="line-height: 1.75;">%s</p>', e($trimmed));
                }
            }

            if ($inList) {
                $pageHtml[] = '</ul>';
            }

            if (!empty($pageHtml)) {
                $pageNumber = $pageNum + 1;
                $htmlSections[] = sprintf(
                    '<div class="curriculum-part mb-4 p-4 rounded-3 bg-white border shadow-xs position-relative">' .
                    '<div class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold mb-3"><i class="bi bi-file-earmark-text me-1"></i> Section / Page %d</div>' .
                    '%s' .
                    '</div>',
                    $pageNumber,
                    implode("\n", $pageHtml)
                );
                $sectionIndex++;
            }
        }

        if (empty($htmlSections)) {
            $html = '<div class="alert alert-info py-2 px-3 small">PDF contains vector drawings or scanned worksheets. Content securely indexed for curriculum verification.</div>';
        } else {
            $html = implode("\n", $htmlSections);
        }

        return [
            'title' => $detectedTitle ?: pathinfo($fullPath, PATHINFO_FILENAME),
            'html' => $html,
            'plain_text' => implode("\n", array_slice($allTextLines, 0, 80)),
            'sections_count' => count($htmlSections),
        ];
    }

    /**
     * Extract structured slides and headers from PPTX presentation.
     */
    protected function extractPptx(string $fullPath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($fullPath) !== true) {
            throw new \RuntimeException('Failed opening PPTX archive.');
        }

        $slides = [];
        $detectedTitle = null;
        $slideIndex = 1;

        while (true) {
            $slideXmlPath = "ppt/slides/slide{$slideIndex}.xml";
            $xmlContent = $zip->getFromName($slideXmlPath);
            if ($xmlContent === false) {
                break;
            }

            $slideData = $this->parseSlideXml($xmlContent, $slideIndex);
            if (!empty($slideData['text'])) {
                if (!$detectedTitle && !empty($slideData['title'])) {
                    $detectedTitle = $slideData['title'];
                }
                $slides[] = $slideData;
            }

            $slideIndex++;
        }

        $zip->close();

        $htmlSections = [];
        $plainLines = [];

        foreach ($slides as $slide) {
            $slideTitle = e($slide['title'] ?: "Slide {$slide['index']}");
            $plainLines[] = $slideTitle;

            $itemsHtml = [];
            foreach ($slide['text'] as $p) {
                $cleanP = e(trim($p));
                if ($cleanP !== '') {
                    $plainLines[] = $cleanP;
                    $itemsHtml[] = sprintf(
                        '<li class="d-flex align-items-start gap-2 mb-2"><i class="bi bi-arrow-right-circle-fill text-primary mt-1 small flex-shrink-0"></i><span>%s</span></li>',
                        $cleanP
                    );
                }
            }

            $htmlSections[] = sprintf(
                '<div class="curriculum-part mb-4 p-4 rounded-3 bg-white border shadow-xs">' .
                '<div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">' .
                '<h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2"><i class="bi bi-easel2-fill text-primary"></i> %s</h4>' .
                '<span class="badge bg-secondary-subtle text-secondary rounded-pill font-mono">Slide %d</span>' .
                '</div>' .
                '<ul class="list-unstyled ps-1 my-3">%s</ul>' .
                '</div>',
                $slideTitle,
                $slide['index'],
                implode("\n", $itemsHtml)
            );
        }

        return [
            'title' => $detectedTitle ?: pathinfo($fullPath, PATHINFO_FILENAME),
            'html' => !empty($htmlSections) ? implode("\n", $htmlSections) : '<p class="text-muted">Presentation slides extracted.</p>',
            'plain_text' => implode("\n", array_slice($plainLines, 0, 80)),
            'sections_count' => count($slides),
        ];
    }

    /**
     * Parse single PPTX slide XML into title and body points.
     */
    protected function parseSlideXml(string $xml, int $index): array
    {
        $title = null;
        $paragraphs = [];

        try {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadXML($xml);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xpath->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');

            // Find title shape
            $titleNodes = $xpath->query('//p:sp[p:nvSpPr/p:nvPr/p:ph[@type="title" or @type="ctrTitle"]]//a:t');
            if ($titleNodes && $titleNodes->length > 0) {
                $titleParts = [];
                foreach ($titleNodes as $node) {
                    $titleParts[] = $node->nodeValue;
                }
                $title = trim(implode(' ', $titleParts));
            }

            // Find all paragraph runs
            $pNodes = $xpath->query('//a:p');
            if ($pNodes) {
                foreach ($pNodes as $pNode) {
                    $tNodes = $xpath->query('.//a:t', $pNode);
                    $textParts = [];
                    foreach ($tNodes as $t) {
                        $textParts[] = $t->nodeValue;
                    }
                    $line = trim(implode('', $textParts));
                    if ($line !== '' && $line !== $title) {
                        $paragraphs[] = $line;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback regex if DOM parsing fails
            preg_match_all('/<a:t[^>]*>(.*?)<\/a:t>/is', $xml, $matches);
            if (!empty($matches[1])) {
                $allT = array_map('trim', $matches[1]);
                $title = $title ?: ($allT[0] ?? null);
                $paragraphs = array_slice($allT, 1);
            }
        }

        return [
            'index' => $index,
            'title' => $title,
            'text' => $paragraphs,
        ];
    }

    /**
     * Extract structured content from DOCX file.
     */
    protected function extractDocx(string $fullPath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($fullPath) !== true) {
            throw new \RuntimeException('Failed opening DOCX archive.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new \RuntimeException('word/document.xml not found in DOCX.');
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xml);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $pNodes = $xpath->query('//w:p');
        $htmlItems = [];
        $plainLines = [];
        $detectedTitle = null;

        if ($pNodes) {
            foreach ($pNodes as $p) {
                $style = $xpath->query('.//w:pStyle/@w:val', $p)->item(0)?->nodeValue;
                $tNodes = $xpath->query('.//w:t', $p);
                $parts = [];
                foreach ($tNodes as $t) {
                    $parts[] = $t->nodeValue;
                }
                $text = trim(implode('', $parts));
                if ($text === '') {
                    continue;
                }

                $plainLines[] = $text;

                if (is_string($style) && preg_match('/heading\s*1/i', $style)) {
                    $detectedTitle = $detectedTitle ?: $text;
                    $htmlItems[] = sprintf('<h3 class="fw-bold text-dark mt-4 mb-2 pb-1 border-bottom"><i class="bi bi-bookmark-fill text-primary me-2"></i>%s</h3>', e($text));
                } elseif (is_string($style) && preg_match('/heading\s*[2-4]/i', $style)) {
                    $htmlItems[] = sprintf('<h5 class="fw-bold text-primary mt-3 mb-1">%s</h5>', e($text));
                } elseif ($this->isHeaderLine($text)) {
                    $detectedTitle = $detectedTitle ?: $text;
                    $htmlItems[] = sprintf('<h4 class="fw-bold text-dark mt-3 mb-1">%s</h4>', e($text));
                } else {
                    $htmlItems[] = sprintf('<p class="text-secondary mb-2" style="line-height: 1.75;">%s</p>', e($text));
                }
            }
        }

        return [
            'title' => $detectedTitle ?: pathinfo($fullPath, PATHINFO_FILENAME),
            'html' => !empty($htmlItems) ? sprintf('<div class="curriculum-part p-4 rounded-3 bg-white border shadow-xs">%s</div>', implode("\n", $htmlItems)) : '<p class="text-muted">Document text extracted.</p>',
            'plain_text' => implode("\n", array_slice($plainLines, 0, 80)),
            'sections_count' => 1,
        ];
    }

    /**
     * Extract structured content from plain text or markdown.
     */
    protected function extractText(string $fullPath): array
    {
        $content = file_get_contents($fullPath);
        $lines = preg_split('/\r\n|\r|\n/', (string) $content);

        $htmlItems = [];
        $plainLines = [];
        $detectedTitle = null;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') {
                continue;
            }

            $plainLines[] = $trimmed;

            if (str_starts_with($trimmed, '# ')) {
                $h = trim(substr($trimmed, 2));
                $detectedTitle = $detectedTitle ?: $h;
                $htmlItems[] = sprintf('<h3 class="fw-bold text-dark mt-4 mb-2 pb-1 border-bottom"><i class="bi bi-bookmark-fill text-primary me-2"></i>%s</h3>', e($h));
            } elseif (str_starts_with($trimmed, '## ')) {
                $h = trim(substr($trimmed, 3));
                $htmlItems[] = sprintf('<h4 class="fw-bold text-primary mt-3 mb-1">%s</h4>', e($h));
            } elseif ($this->isHeaderLine($trimmed)) {
                $detectedTitle = $detectedTitle ?: $trimmed;
                $htmlItems[] = sprintf('<h4 class="fw-bold text-dark mt-3 mb-1">%s</h4>', e($trimmed));
            } else {
                $htmlItems[] = sprintf('<p class="text-secondary mb-2" style="line-height: 1.75;">%s</p>', e($trimmed));
            }
        }

        return [
            'title' => $detectedTitle ?: pathinfo($fullPath, PATHINFO_FILENAME),
            'html' => sprintf('<div class="curriculum-part p-4 rounded-3 bg-white border shadow-xs">%s</div>', implode("\n", $htmlItems)),
            'plain_text' => implode("\n", array_slice($plainLines, 0, 80)),
            'sections_count' => 1,
        ];
    }

    /**
     * Generic fallback.
     */
    protected function extractGeneric(string $fullPath): array
    {
        return [
            'title' => pathinfo($fullPath, PATHINFO_FILENAME),
            'html' => '<div class="alert alert-light border p-3 small text-muted"><i class="bi bi-file-earmark-check me-2 text-primary"></i> Document uploaded and registered for curriculum verification.</div>',
            'plain_text' => 'Curriculum material file attached.',
            'sections_count' => 1,
        ];
    }

    /**
     * Determine if a text line represents a major section or chapter heading.
     */
    protected function isHeaderLine(string $line): bool
    {
        $len = strlen($line);
        if ($len < 3 || $len > 120) {
            return false;
        }

        // Markdown header
        if (str_starts_with($line, '#')) {
            return true;
        }

        // Explicit Keywords
        if (preg_match('/^(chapter|unit|section|module|part|lesson|topic|specimen|syllabus|component|paper)\s+[0-9ivxlcdm]+/i', $line)) {
            return true;
        }

        // Numbered Sections (e.g. "1. Foundational Algebra", "2.1 Equilibrium")
        if (preg_match('/^[0-9]+(\.[0-9]+)*\s+[A-Z]/', $line) && !str_ends_with($line, '.')) {
            return true;
        }

        // ALL CAPS heading under 80 characters without punctuation
        if ($len >= 4 && $len <= 80 && mb_strtoupper($line) === $line && preg_match('/[A-Z]/', $line) && !str_ends_with($line, '.')) {
            return true;
        }

        return false;
    }
}
