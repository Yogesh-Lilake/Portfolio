<?php
namespace app\JsonValidators\Global;

use app\JsonValidators\Contracts\JsonValidatorInterface;

/**
 * HeaderSettingJsonValidator
 *
 * SECURITY PROFILE:
 * - Header JSON affects routing, links, branding
 * - This is SECURITY-SENSITIVE configuration
 * - Schema MUST be CLOSED
 * - No HTML / JS / markup allowed at all
 *
 * Failure here must:
 *  - Reject JSON
 *  - Log warning
 *  - Fall back to hard-coded defaults
 */
class HeaderSettingJsonValidator implements JsonValidatorInterface
{
    /**
     * Stores last validation failure reason (DC code)
     */
    private ?string $errorCode = null;

    /**
     * Closed schema definition
     * ----------------------------------
     * Header JSON must contain ONLY these keys.
     * Any unknown key indicates schema drift,
     * bad deploy, or hidden attack surface.
     */
    private array $allowedKeys = [
        'site_title',
        'logo_path',
        'button_text',
        'button_link',
        'accent_color'
    ];

    /**
     * Required keys (subset of allowed keys)
     */
    private array $requiredKeys = [
        'site_title',
        'logo_path',
        'button_text',
        'button_link',
        'accent_color'
    ];

    /**
     * Validate header.json structure + semantics
     */
    public function validate(array $data): bool
    {
        /* ============================================================
         * DC-12 — CLOSED SCHEMA ENFORCEMENT
         * ============================================================ */
        $unknownKeys = array_diff(array_keys($data), $this->allowedKeys);

        if (!empty($unknownKeys)) {
            $this->errorCode =
                'DC-12: header.json contains unknown keys: ' .
                implode(', ', $unknownKeys);
            return false;
        }

        /* ============================================================
         * DC-09 — REQUIRED KEYS
         * ============================================================ */
        foreach ($this->requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                $this->errorCode =
                    "DC-09: header.json missing key '{$key}'";
                return false;
            }
        }

        /* ============================================================
         * DC-10 — TYPE + EMPTY VALIDATION
         * ============================================================ */
        foreach ($this->requiredKeys as $key) {
            if (!is_string($data[$key]) || trim($data[$key]) === '') {
                $this->errorCode =
                    "DC-10: header.json invalid or empty '{$key}'";
                return false;
            }
        }

        /* ============================================================
         * DC-11 — SEMANTIC / SECURITY VALIDATION
         * ============================================================ */

        foreach (['site_title', 'logo_path', 'button_text'] as $field) {
            if ($this->containsMarkup($data[$field])) {
                $this->errorCode = "DC-11: header.json unsafe content in '{$field}'";
                return false;
            }
        }

        // Validate logo path format
        if (!$this->isValidLogoPath($data['logo_path'])) {
            $this->errorCode = "DC-11: header.json invalid logo_path";
            return false;
        }

        // Accent color must be valid hex
        if (!$this->isValidHexColor($data['accent_color'])) {
            $this->errorCode =
                'DC-11: header.json invalid accent_color';
            return false;
        }

        // CTA link must be safe internal route
        if (!$this->isSafeInternalLink($data['button_link'])) {
            $this->errorCode =
                'DC-11: header.json unsafe button_link';
            return false;
        }

        // Passed all checks — trusted JSON
        return true;
    }

    /**
     * Return last validation error code (for logging)
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /* ============================================================
       INTERNAL SEMANTIC HELPERS
    ============================================================ */

    /**
     * Validate NO HTML / JS
     */
    private function containsMarkup(string $value): bool
    {
        return preg_match('/<[^>]*>|javascript:|on\w+=/i', $value);
    }

    /**
     * Validate logo path
     */
    private function isValidLogoPath(string $path): bool
    {
        // No protocols
        if (preg_match('#^(https?:|data:|javascript:)#i', $path)) {
            return false;
        }

        // Only allow filename or subpath
        return preg_match('#^[a-zA-Z0-9/_\-\.]+\.(png|jpg|jpeg|svg|webp)$#', $path);
    }


    /**
     * Validate hex color (#RGB or #RRGGBB)
     */
    private function isValidHexColor(string $color): bool
    {
        return (bool) preg_match(
            '/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/',
            $color
        );
    }

    /**
     * Validate safe internal links only
     *
     * Allowed:
     *  /contact
     *  contact
     *  /projects
     *
     * Disallowed:
     *  <script>
     *  javascript:
     *  http://
     *  https://
     */
    private function isSafeInternalLink(string $link): bool
    {
        // Reject script-like / injection characters
        if (preg_match('/[<>"\'\(\);]/', $link)) {
            return false;
        }

        // Normalize to route form
        $link = '/' . trim($link, '/');

        // Allow only known application routes
        return in_array($link, [
            '/',
            '/about',
            '/projects',
            '/notes',
            '/contact',
            '/downloadcv'
        ], true);
    }
}
