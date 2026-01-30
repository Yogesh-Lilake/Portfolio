<?php

/**
 * VIEW HELPERS
 *
 * Purpose:
 * - Centralize output escaping
 * - Prevent XSS
 * - Keep views clean and dumb
 *
 * RULE:
 *  Never use htmlspecialchars() directly in views
 *  Always use helpers below
 */

/**
 * safe()
 * --------------------------------------------------
 * WHEN TO USE:
 * - Plain text output
 * - Titles, labels, names
 * - Content already validated but needs escaping
 *
 * CONTEXT:
 * HTML text nodes or attributes
 */
function safe($value, string $default = ''): string
{
    if (!isset($value) || $value === '' || $value === false || $value === null) {
        return $default;
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * field()
 * --------------------------------------------------
 * WHEN TO USE:
 * - Array-based view data
 * - Optional fields
 *
 * EXAMPLE:
 * field($header, 'site_title', 'My Site')
 */
function field(array $array, string $key, string $default = ''): string
{
    return isset($array[$key]) ? safe($array[$key], $default) : $default;
}

/**
 * safeStr()
 * --------------------------------------------------
 * WHEN TO USE:
 * - Previews
 * - Excerpts
 * - Limited-length UI text
 */
function safeStr($value, int $length = 120, string $default = ''): string
{
    if (!isset($value) || $value === '' || $value === false || $value === null) {
        return $default;
    }

    return htmlspecialchars(
        mb_substr((string) $value, 0, $length),
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * url()
 * --------------------------------------------------
 * WHEN TO USE:
 * - Internal application routes
 *
 * IMPORTANT:
 * - This does NOT escape HTML
 * - Use safe() when echoing
 */
function url(string $path = ''): string
{
    $path = trim($path);

    // Absolute URL → return as-is
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    return BASE_URL . ltrim($path, '/');
}

/**
 * safe_url()
 * --------------------------------------------------
 * WHEN TO USE:
 * - User-provided external URLs (social links, etc.)
 *
 * RULE:
 * - Only http/https allowed
 */
function safe_url($value, string $default = ''): string
{
    if (!isset($value) || $value === '') {
        return $default;
    }

    $value = trim((string) $value);

    /**
     * Allow ONLY:
     *  - https://  (external links)
     *  - mailto:   (email links)
     */
    if (!preg_match('#^(https://|mailto:)#i', $value)) {
        return $default;
    }
    
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * logo()
 * --------------------------------------------------
 * WHEN TO USE:
 * - Logo image path
 * - Header / footer branding
 *
 * RESPONSIBILITY:
 * - Always return a valid image path
 */
function logo(?string $path): string
{
    // Empty or non-string
    if (!is_string($path) || trim($path) === '') {
        app_log('logo(): default logo used (empty)', 'warning');
        return IMG_URL . 'default_logo.png';
    }

    // Reject markup or JS-like content
    if (preg_match('/<[^>]*>|javascript:|on\w+=/i', $path)) {
        app_log('logo(): unsafe logo_path rejected', 'warning');
        return IMG_URL . 'default_logo.png';
    }

    // Reject external / protocol paths
    if (preg_match('#^(https?:|data:)#i', $path)) {
        app_log('logo(): external logo_path rejected', 'warning');
        return IMG_URL . 'default_logo.png';
    }

    return IMG_URL . ltrim($path, '/');
}


/**
 * asset()
 * --------------------------------------------------
 * WHEN TO USE:
 * - JS, CSS, images inside public/assets
 */
function asset(string $path): string
{
    return PROJECT_URL . ltrim($path, '/');
}
