<?php

class Brand
{
    /**
     * Get the short brand display name.
     */
    public static function name(): string
    {
        return config('branding.name', 'Scroll');
    }

    /**
     * Get the full brand name (used in page titles, etc.).
     */
    public static function fullName(): string
    {
        return config('branding.full_name', 'Scroll Payroll System');
    }

    /**
     * Get the brand tagline.
     */
    public static function tagline(): string
    {
        return config('branding.tagline', 'Payroll system built for remote-first companies');
    }

    /**
     * Get the primary / accent CSS color.
     */
    public static function primaryColor(): string
    {
        return config('branding.primary_color', '#0d6efd');
    }

    /**
     * Get the asset URL for the main logo.
     *
     * @param  string|null  $fallback  Optional override path if config is empty.
     */
    public static function logo(?string $fallback = null): string
    {
        $path = config('branding.logo') ?: $fallback;

        return asset($path ?: 'assets/images/logo-scroll.png');
    }

    /**
     * Get the asset URL for the white/inverted logo.
     *
     * @param  string|null  $fallback  Optional override path if config is empty.
     */
    public static function logoWhite(?string $fallback = null): string
    {
        $path = config('branding.logo_white') ?: $fallback;

        return asset($path ?: 'assets/images/logo-brand-white.png');
    }

    /**
     * Get the asset URL for the favicon.
     *
     * @param  string|null  $fallback  Optional override path if config is empty.
     */
    public static function favicon(?string $fallback = null): string
    {
        $path = config('branding.favicon') ?: $fallback;

        return asset($path ?: 'assets/images/logo.png');
    }

    /**
     * Get the asset URL for the fallback/default avatar image.
     */
    public static function fallbackAvatar(): string
    {
        $path = config('branding.fallback_avatar') ?: 'assets/images/logo.png';

        return asset($path);
    }

    /**
     * Get the absolute filesystem path for the logo (for PDF generation / DOMPDF).
     */
    public static function logoPath(): string
    {
        $path = config('branding.logo') ?: 'assets/images/logo-scroll.png';

        return public_path($path);
    }

    /**
     * Get the footer text. Returns custom text if set, otherwise generates from brand name.
     */
    public static function footerText(): string
    {
        $custom = config('branding.footer_text');

        if ($custom !== '' && $custom !== null) {
            return $custom;
        }

        $owner = config('branding.copyright_owner') ?: self::name();

        return '© '.date('Y')." {$owner}. All rights reserved.";
    }

    /**
     * Get the copyright line, e.g. "© 2026 MyCompany".
     */
    public static function copyrightLine(): string
    {
        $owner = config('branding.copyright_owner') ?: self::name();

        return '© '.date('Y')." {$owner}";
    }

    /**
     * Get the GitHub repository URL.
     */
    public static function githubUrl(): string
    {
        return config('branding.github_url', 'https://github.com/Sultonisky/Scroll');
    }

    /**
     * Get the "Built by Author" HTML string for the footer.
     */
    public static function authorHtml(): string
    {
        $name = config('branding.author_name', 'Mohammad Sultoni');
        $url = config('branding.author_url', 'https://github.com/Sultonisky');

        return "Built by <a href=\"{$url}\" target=\"_blank\" rel=\"noopener noreferrer\">{$name}</a>";
    }
}
