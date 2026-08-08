<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site-level SEO defaults
    |--------------------------------------------------------------------------
    | These values are used by the guest layout as fallbacks when a page does
    | not supply its own SEO props.  Override any value per-page by passing
    | props to <x-layouts.guest>, or inject arbitrary <head> tags via
    | @push('head') / @push('schema') from within any view.
    |
    | Set values via .env where they differ between environments (e.g. staging
    | should typically have robots set to "noindex, nofollow").
    */

    /*
    |--------------------------------------------------------------------------
    | Basic metadata
    |--------------------------------------------------------------------------
    */

    // Shown in the meta description tag when no page-specific description is set.
    'description' => env('SEO_DESCRIPTION', 'Your go-to platform for modern, streamlined school management.'),

    // Short brand tagline appended to the app name when no description is set.
    // Used as an absolute last-resort fallback only.
    'tagline' => env('SEO_TAGLINE', ''),

    // Comma-separated keywords (low search-engine weight but harmless).
    'keywords' => env('SEO_KEYWORDS', ''),

    // IETF language + territory used for og:locale.
    'locale' => env('SEO_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Social sharing image (Open Graph / Twitter Card)
    |--------------------------------------------------------------------------
    | Supply either a full URL or a path relative to the public directory.
    | Recommended dimensions: 1200 × 630 px, < 1 MB.
    */

    'og_image'        => env('SEO_OG_IMAGE', 'images/og-default.jpg'),
    'og_image_width'  => 1200,
    'og_image_height' => 630,

    /*
    |--------------------------------------------------------------------------
    | Twitter
    |--------------------------------------------------------------------------
    */

    'twitter' => [
        // Site-level Twitter/X handle including the @ sign, e.g. "@myapp".
        'handle' => env('SEO_TWITTER_HANDLE', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme colour (mobile browser chrome)
    |--------------------------------------------------------------------------
    | Shown in supported mobile browsers as the browser toolbar colour.
    | Supply separate light/dark values to match your palette.
    */

    'theme_color'      => env('SEO_THEME_COLOR',      '#3B82F6'), // blue-500
    'theme_color_dark' => env('SEO_THEME_COLOR_DARK',  '#1e293b'), // slate-800

];