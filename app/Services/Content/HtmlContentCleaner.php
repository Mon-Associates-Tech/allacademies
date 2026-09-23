<?php

namespace App\Services\Content;

class HtmlContentCleaner
{
    public function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        // Remove HTML comments, including Word conditional comments.
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? $html;

        // If you use mews/purifier, this is the preferred path.
        if (app()->bound('purifier')) {
            return app('purifier')->clean($html, [
                'HTML.Allowed' => 'p,br,strong,em,b,i,u,sup,sub,span[style],ul,ol,li,table,thead,tbody,tr,th,td,a[href|target],img[src|alt|width|height]',
                'CSS.AllowedProperties' => [
                    'font-style',
                    'font-weight',
                    'color',
                    'background-color',
                    'text-align',
                    'vertical-align',
                ],
                'Attr.AllowedFrameTargets' => ['_blank'],
            ]);
        }

        // Fallback: basic tag allowlist.
        $html = strip_tags(
            $html,
            '<p><br><strong><em><b><i><u><sup><sub><span><ul><ol><li><table><thead><tbody><tr><th><td><a>'
        );

        // Rough fallback cleanup for dangerous attributes.
        $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/\sclass\s*=\s*("[^"]*Mso[^"]*"|\'[^\']*Mso[^\']*\'|[^\s>]*Mso[^\s>]*)/i', '', $html) ?? $html;
        $html = preg_replace('/\sstyle\s*=\s*("[^"]*mso-[^"]*"|\'[^\']*mso-[^\']*\'|[^\s>]*mso-[^\s>]*)/i', '', $html) ?? $html;

        return trim($html) ?: null;
    }
}
