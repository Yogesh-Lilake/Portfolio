<?php
namespace app\CacheValidators;

class HomePageCacheValidator implements CacheValidatorInterface
{
    /**
     * Page-level Home cache only
     */
    public function supports(string $key): bool
    {
        return $key === 'v1_home_page';
    }

    /**
     * Validate full Home page cache payload
     *
     * Invariants:
     * - Home page cache represents a FULL DB snapshot
     * - No fallback or degraded section may be cached
     * - Schema is CLOSED (no unknown keys)
     */
    public function validate(array $payload): ?string
    {
        /* =====================================================
           CLOSED PAGE SCHEMA
        ===================================================== */
        $requiredSections = [
            'safe_mode',
            'home',
            'about',
            'skills',
            'projects',
            'contact',
        ];

        // Reject unknown keys
        foreach ($payload as $key => $_) {
            if (!in_array($key, $requiredSections, true)) {
                return "DC-04 Home page schema violation (unexpected key '{$key}')";
            }
        }

        // Ensure all required sections exist
        foreach ($requiredSections as $section) {
            if (!array_key_exists($section, $payload)) {
                return "DC-04 Home page schema missing section '{$section}'";
            }
        }

        /* =====================================================
           SAFE MODE CONTRACT
        ===================================================== */
        if ($payload['safe_mode'] !== false) {
            return "DC-05 Home page semantic violation (safe_mode must never be cached as true)";
        }

        /* =====================================================
           SECTION CONTRACT + TRUST CONSISTENCY
        ===================================================== */
        foreach ($payload as $section => $block) {

            if ($section === 'safe_mode') {
                continue;
            }

            /* ---------- STRUCTURE ---------- */
            if (!is_array($block)) {
                return "DC-04 Home page section '{$section}' invalid structure";
            }

            if (!array_key_exists('from_db', $block) || !array_key_exists('data', $block)) {
                return "DC-04 Home page section '{$section}' missing contract keys";
            }

            if (!is_bool($block['from_db'])) {
                return "DC-05 Home page section '{$section}' semantic violation (from_db not boolean)";
            }

            if (!is_array($block['data'])) {
                return "DC-03 Home page section '{$section}' payload corrupted";
            }

            // Page cache must NEVER include fallback data
            if ($block['from_db'] !== true) {
                return "DC-05 Home page trust violation (page cache contains non-DB section '{$section}')";
            }

            // DB trust implies non-empty data
            if (empty($block['data'])) {
                return "DC-05 Home page section '{$section}' semantic corruption (from_db=true but data empty)";
            }
        }

        /* =====================================================
           CACHE IS TRUSTWORTHY
        ===================================================== */
        return null;
    }
}
