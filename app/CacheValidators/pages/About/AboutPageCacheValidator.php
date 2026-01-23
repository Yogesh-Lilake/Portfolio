<?php
namespace app\CacheValidators\Pages\About;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class AboutPageCacheValidator implements CacheValidatorInterface
{
    /**
     * Page-level About cache only
     */
    public function supports(string $key): bool
    {
        return $key === 'v1_about_page';
    }

    /**
     * Validate full About page cache payload
     */
    public function validate(array $payload): ?string
    {
        /* =====================================================
           REQUIRED PAGE-LEVEL SECTIONS (CLOSED SCHEMA)
        ===================================================== */
        $requiredSections = [
            'safe_mode',
            'hero',
            'content',
            'skills',
            'experience',
            'education',
            'stats',
        ];

        // Reject unknown keys (schema hardening)
        foreach ($payload as $key => $_) {
            if (!in_array($key, $requiredSections, true)) {
                return "DC-04 About page schema violation (unexpected key '{$key}')";
            }
        }

        // Ensure all required keys exist
        foreach ($requiredSections as $section) {
            if (!array_key_exists($section, $payload)) {
                return "DC-04 About page schema missing section '{$section}'";
            }
        }

        /* =====================================================
           SAFE MODE CONTRACT (CRITICAL)
        ===================================================== */
        if ($payload['safe_mode'] !== false) {
            return "DC-05 About page semantic violation (safe_mode must never be cached as true)";
        }

        /* =====================================================
           SECTION CONTRACT + TRUST CONSISTENCY
        ===================================================== */
        foreach ($payload as $section => $block) {

            if ($section === 'safe_mode') {
                continue;
            }

            // Structure
            if (!is_array($block)) {
                return "DC-04 About page section '{$section}' invalid structure";
            }

            if (!array_key_exists('from_db', $block) || !array_key_exists('data', $block)) {
                return "DC-04 About page section '{$section}' missing contract keys";
            }

            // from_db must be boolean
            if (!is_bool($block['from_db'])) {
                return "DC-05 About page section '{$section}' semantic violation (from_db not boolean)";
            }

            // data must be array
            if (!is_array($block['data'])) {
                return "DC-03 About page section '{$section}' payload corrupted";
            }

            // DB trust implies non-empty data
            if ($block['from_db'] === true && empty($block['data'])) {
                return "DC-05 About page section '{$section}' semantic corruption (from_db=true but data empty)";
            }

            //  Page cache must NEVER contain fallback data
            if ($block['from_db'] === false) {
                return "DC-05 About page trust violation (page cache contains non-DB section '{$section}')";
            }
        }

        /* =====================================================
           CACHE IS TRUSTWORTHY
        ===================================================== */
        return null;
    }
}
