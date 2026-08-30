<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class StaticProse
{
    public function render(?string $content, bool $isHtml = false): string
    {
        if ($content === null || trim($content) === '') {
            return '';
        }

        return Cache::rememberForever(
            'static-prose:'.md5($content.($isHtml ? ':html' : ':md')),
            fn () => $this->doRender($content, $isHtml)
        );
    }

    private function doRender(string $content, bool $isHtml): string
    {
        $maths = [];

        // 1. Pull out TeX (optionally wrapped in backticks) → placeholders
        $pattern = '/`?(\$\$.+?\$\$|\\\\\\[[\s\S]+?\\\\\\]|\$[^$\n]+?\$|\\\\\\([\s\S]+?\\\\\\))`?/s';

        $content = preg_replace_callback($pattern, function (array $m) use (&$maths) {
            $raw = $m[1];
            $display = false;

            if (str_starts_with($raw, '$$') && str_ends_with($raw, '$$')) {
                $tex = substr($raw, 2, -2); $display = true;
            } elseif (str_starts_with($raw, '\\[') && str_ends_with($raw, '\\]')) {
                $tex = substr($raw, 2, -2); $display = true;
            } elseif (str_starts_with($raw, '\\(') && str_ends_with($raw, '\\)')) {
                $tex = substr($raw, 2, -2);
            } else {
                $tex = trim($raw, '$');
            }

            $maths[] = ['tex' => trim($tex), 'displayMode' => $display];

            return '<span class="math-ph">'.(count($maths) - 1).'</span>';
        }, $content);

        // 2. Markdown → HTML (skip for TinyMCE HTML content)
        if (! $isHtml) {
            $content = (string) (new GithubFlavoredMarkdownConverter([
                'html_input' => 'allow',
                'allow_unsafe_links' => false,
            ]))->convert($content);
        }

        // 3. Render all math in a single node call and swap placeholders back
        $rendered = $this->katexBatch($maths);

        foreach ($rendered as $i => $html) {
            $content = str_replace('<span class="math-ph">'.$i.'</span>', $html, $content);
        }

        return $content;
    }

    private function katexBatch(array $items): array
    {
        if (empty($items)) {
            return [];
        }

        $fallback = fn () => array_map(fn ($i) => '<code>'.$i['tex'].'</code>', $items);

        $result = Process::input(json_encode($items, JSON_UNESCAPED_UNICODE))
            ->timeout(30)
            ->run(['node', base_path('scripts/katex-batch.js')]);

        if (! $result->successful()) {
            return $fallback();
        }

        $decoded = json_decode($result->output(), true);

        return (is_array($decoded) && count($decoded) === count($items))
            ? array_column($decoded, 'html')
            : $fallback();
    }
}
