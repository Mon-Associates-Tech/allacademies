<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use RuntimeException;
use Throwable;

class MarkdownMathService
{
    private const PLACEHOLDER_START = "\x02";
    private const PLACEHOLDER_END = "\x03";

    private GithubFlavoredMarkdownConverter $converter;

    public function __construct()
    {
        $this->converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Render a single raw markdown string (with optional $...$/$$...$$/\(...\)/\[...\]
     * math) to safe HTML.
     */
    public function render(?string $markdown): string
    {
        if (!is_string($markdown) || trim($markdown) === '') {
            return '';
        }

        return $this->renderMany([$markdown])[0];
    }

    /**
     * Render many raw markdown strings in one pass. All math across every string
     * is batched into a single Node process call — use this for backfills, where
     * one Node invocation per chunk beats one per record.
     *
     * @param array<int|string, string|null> $markdownStrings
     * @return array<int|string, string> HTML, keyed identically to the input
     */
    public function renderMany(array $markdownStrings): array
    {
        $protectedCode = [];
        $mathExpressions = [];
        $prepared = [];

    foreach ($markdownStrings as $key => $markdown) {
        if (!is_string($markdown) || trim($markdown) === '') {
            $prepared[$key] = '';
            continue;
        }

        $text = $this->unwrapBacktickedMath($markdown);
        $text = $this->protectFencedCode($text, $protectedCode);
        $text = $this->extractMath($text, $mathExpressions);
        $text = $this->restoreFencedCode($text, $protectedCode);

        $prepared[$key] = $text;
    }

        $renderedMath = $mathExpressions === [] ? [] : $this->safeRenderMathBatch($mathExpressions);

        $html = [];
        foreach ($prepared as $key => $text) {
            $html[$key] = $text === ''
                ? ''
                : $this->injectMath((string) $this->converter->convert($text), $renderedMath);
        }

        return $html;
    }

    /**
     * Pull ```fenced``` code blocks out before math extraction, so a literal $
     * inside a code sample (shell examples, ICT/computing questions, etc.) is
     * never mistaken for math. Restored before CommonMark sees the text, so
     * fences still render/highlight normally.
     */
    private function protectFencedCode(string $markdown, array &$store): string
    {
        return preg_replace_callback('/```.*?```/s', function (array $m) use (&$store) {
            $token = self::PLACEHOLDER_START . 'CODE' . count($store) . self::PLACEHOLDER_END;
            $store[$token] = $m[0];
            return $token;
        }, $markdown);
    }

    private function restoreFencedCode(string $text, array $store): string
    {
        return strtr($text, $store);
    }

    /**
     * Replace $$...$$, \[...\], \(...\), $...$ with placeholder tokens, appending
     * the raw TeX + display mode to $expressions (passed by reference so the
     * index stays global across every string in a renderMany() batch).
     */
    private function extractMath(string $text, array &$expressions): string
    {
        $patterns = [
            '/\$\$(.+?)\$\$/s' => true,          // $$ ... $$
            '/\\\\\[(.+?)\\\\\]/s' => true,       // \[ ... \]
            '/\\\\\((.+?)\\\\\)/s' => false,      // \( ... \)
            '/\$(?!\$)([^\$\n]+?)\$/' => false,   // $ ... $  (single line only)
        ];

        foreach ($patterns as $pattern => $displayMode) {
            $text = preg_replace_callback($pattern, function (array $m) use (&$expressions, $displayMode) {
                $index = count($expressions);
                $expressions[] = ['tex' => trim($m[1]), 'displayMode' => $displayMode];
                return self::PLACEHOLDER_START . 'MATH' . $index . self::PLACEHOLDER_END;
            }, $text);
        }

        return $text;
    }

    private function injectMath(string $html, array $renderedMath): string
    {
        return preg_replace_callback('/\x02MATH(\d+)\x03/', function (array $m) use ($renderedMath) {
            return $renderedMath[(int) $m[1]]['html'] ?? '';
        }, $html);
    }

    /**
     * Batch-render through katex-batch.js, but never let a missing/broken Node
     * pipeline take the whole page down — fall back to literal $...$ text so
     * content still renders while Node/KaTeX isn't provisioned everywhere yet.
     */
    private function safeRenderMathBatch(array $expressions): array
    {
        try {
            return $this->renderMathBatch($expressions);
        } catch (Throwable $e) {
            Log::warning('KaTeX batch render failed, falling back to raw TeX: ' . $e->getMessage());

            $fallback = [];
            foreach ($expressions as $i => $expr) {
                $wrap = $expr['displayMode'] ? '$$' : '$';
                $fallback[$i] = ['html' => $wrap . e($expr['tex']) . $wrap];
            }
            return $fallback;
        }
    }

    /**
     * @param array<int, array{tex: string, displayMode: bool}> $expressions
     * @return array<int, array{html: string}>
     */
    private function renderMathBatch(array $expressions): array
    {
        $scriptPath = config('services.katex.script_path');

        if (!is_string($scriptPath) || !is_file($scriptPath)) {
            throw new RuntimeException("KaTeX batch script not found at [{$scriptPath}].");
        }

        $result = Process::input(json_encode(array_values($expressions)))
            ->timeout(30)
            ->run(['node', $scriptPath]);

        if ($result->failed()) {
            throw new RuntimeException('katex-batch.js exited with an error: ' . $result->errorOutput());
        }

        $decoded = json_decode($result->output(), true);

        if (!is_array($decoded)) {
            throw new RuntimeException('katex-batch.js returned invalid JSON.');
        }

        return $decoded;
    }

    /**
 * Authors frequently wrap LaTeX in single backticks (`$...$`), treating math
 * like inline code — likely a habit carried over from copy-pasting question
 * banks generated elsewhere. Strip the backticks when they exactly wrap a
 * complete math delimiter pair, so the expression reaches extractMath() as
 * plain $...$ / \(...\) / \[...\] rather than being fenced off as code.
 */
private function unwrapBacktickedMath(string $text): string
{
    $patterns = [
        '/`(\$\$.+?\$\$)`/s',
        '/`(\\\\\[.+?\\\\\])`/s',
        '/`(\\\\\(.+?\\\\\))`/s',
        '/`(\$[^`$\n]+?\$)`/',
    ];

    foreach ($patterns as $pattern) {
        $text = preg_replace($pattern, '$1', $text);
    }

    return $text;
}
}