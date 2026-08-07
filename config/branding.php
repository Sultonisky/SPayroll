<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand Name
    |--------------------------------------------------------------------------
    |
    | Short display name used across the UI sidebar, nav, headings, etc.
    |
    */

    'name' => env('BRAND_NAME', 'Scroll'),

    /*
    |--------------------------------------------------------------------------
    | Brand Full Name
    |--------------------------------------------------------------------------
    |
    | Full brand name used in page titles and formal references.
    |
    */

    'full_name' => env('BRAND_FULL_NAME', 'Scroll Payroll System'),

    /*
    |--------------------------------------------------------------------------
    | Brand Tagline
    |--------------------------------------------------------------------------
    |
    | Short tagline displayed on the login page and other branding surfaces.
    |
    */

    'tagline' => env('BRAND_TAGLINE', 'Payroll system built for remote-first companies'),

    /*
    |--------------------------------------------------------------------------
    | Primary Color
    |--------------------------------------------------------------------------
    |
    | CSS color value used for accent/brand color across the UI.
    |
    */

    'primary_color' => env('BRAND_PRIMARY_COLOR', '#0d6efd'),

    /*
    |--------------------------------------------------------------------------
    | Logo Path
    |--------------------------------------------------------------------------
    |
    | Path to the main logo image, relative to the public/ directory.
    |
    */

    'logo' => env('BRAND_LOGO', 'assets/images/logo-scroll.png'),

    /*
    |--------------------------------------------------------------------------
    | White Logo Path
    |--------------------------------------------------------------------------
    |
    | Path to the white/inverted logo image for dark backgrounds.
    |
    */

    'logo_white' => env('BRAND_LOGO_WHITE', 'assets/images/logo-brand-white.png'),

    /*
    |--------------------------------------------------------------------------
    | Favicon Path
    |--------------------------------------------------------------------------
    |
    | Path to the favicon image, relative to the public/ directory.
    |
    */

    'favicon' => env('BRAND_FAVICON', 'assets/images/logo.png'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Avatar Path
    |--------------------------------------------------------------------------
    |
    | Path to the default avatar image used when a user has no profile photo.
    |
    */

    'fallback_avatar' => env('BRAND_FALLBACK_AVATAR', 'assets/images/logo.png'),

    /*
    |--------------------------------------------------------------------------
    | Footer Text
    |--------------------------------------------------------------------------
    |
    | Custom footer text. Leave empty to auto-generate from brand name.
    |
    */

    'footer_text' => env('BRAND_FOOTER_TEXT', ''),

    /*
    |--------------------------------------------------------------------------
    | Copyright Owner
    |--------------------------------------------------------------------------
    |
    | The name shown in the copyright line. Leave empty to use brand name.
    |
    */

    'copyright_owner' => env('BRAND_COPYRIGHT_OWNER', ''),

    /*
    |--------------------------------------------------------------------------
    | GitHub URL
    |--------------------------------------------------------------------------
    |
    | URL to the project's GitHub repository.
    |
    */

    'github_url' => env('BRAND_GITHUB_URL', 'https://github.com/Sultonisky/Scroll'),

    /*
    |--------------------------------------------------------------------------
    | Author Name
    |--------------------------------------------------------------------------
    |
    | Name of the original author / maintainer shown in footer credits.
    |
    */

    'author_name' => env('BRAND_AUTHOR_NAME', 'Mohammad Sultoni'),

    /*
    |--------------------------------------------------------------------------
    | Author URL
    |--------------------------------------------------------------------------
    |
    | URL linked from the author name in footer credits.
    |
    */

    'author_url' => env('BRAND_AUTHOR_URL', 'https://github.com/Sultonisky'),

];
