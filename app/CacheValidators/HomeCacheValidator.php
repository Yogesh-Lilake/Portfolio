<?php
namespace app\CacheValidators;

class HomeCacheValidator implements CacheValidatorInterface
{
    /**
     * Page-level cache only
     */
    public function supports(string $key): bool
    {
        return $key === 'v1_home_page';
    }

    /**
     * Validate full home page cache payload
     *
     * Rules:
     * - Page cache must be fully self-consistent
     * - No section may silently degrade
     * - If any section violates contract → cache is invalid
     */
    public function validate(array $payload): ?string
    {
        /* =====================================================
           REQUIRED PAGE-LEVEL KEYS
        ===================================================== */
        $requiredSections = [
            'safe_mode',
            'home',
            'about',
            'skills',
            'projects',
            'contact',
        ];

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
           SECTION CONTRACT VALIDATION
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

            /* =================================================
               SEMANTIC RULE: from_db implies non-empty data
            ================================================= */
            if ($block['from_db'] === true && empty($block['data'])) {
                return "DC-05 Home page section '{$section}' semantic corruption (from_db=true but data empty)";
            }
        }

        /* =====================================================
           CACHE IS TRUSTWORTHY
        ===================================================== */
        return null;
    }
}
