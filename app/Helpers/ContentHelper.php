<?php

namespace App\Helpers;

class ContentHelper
{
    /**
     * Process content from TinyMCE editor
     * Handles markdown-style syntax that may be pasted into the editor
     */
    public static function processEditorContent(string $content): string
    {
        // Convert **bold** to <strong>bold</strong>
        $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);

        // Convert *italic* to <em>italic</em> (but not already processed **)
        $content = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/', '<em>$1</em>', $content);

        // Convert `code` to <code>code</code>
        $content = preg_replace('/`(.+?)`/', '<code>$1</code>', $content);

        // Convert line breaks with list markers to actual HTML lists
        $content = preg_replace_callback(
            '/(<p>)?(\s*-\s+(.+?)(<\/p>)?)\n?/s',
            function($matches) {
                static $inList = false;
                $item = '<li>' . trim($matches[3]) . '</li>';

                if (!$inList) {
                    $inList = true;
                    return '<ul>' . $item;
                } else {
                    return $item;
                }
            },
            $content
        );

        // Close any unclosed lists
        if (substr_count($content, '<ul>') > substr_count($content, '</ul>')) {
            $content .= '</ul>';
        }

        // Clean up any double paragraph tags
        $content = str_replace(['<p><p>', '</p></p>'], ['<p>', '</p>'], $content);

        return $content;
    }
}
