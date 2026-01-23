<?php
namespace app\CacheValidators;

class ContactHeroSectionCacheValidator implements CacheValidatorInterface
{
    /**
     * Section-level Contact Hero cache
     */
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_hero';
    }

    /**
     * Validate Contact Hero section cache
     *
     * Invariants:
     * - Section cache must be DB-trusted only
     * - Schema is CLOSED
     * - Data must be UI-safe for contact.php
     */
    public function validate(array $payload): ?string
    {
        
        $allowedKeys = [
            'id',
            'hero_heading',
            'hero_subheading',
            'hero_lottie_url',
            'section_heading',
            'intro_text',
            'map_embed_url',
            'toast_message',
            'is_active',
            'updated_at',
        ];

        /* =====================================================
           CLOSED SCHEMA
        ===================================================== */
        foreach ($payload as $key => $_) {
            if (!in_array($key, $allowedKeys, true)) {
                return "DC-04 ContactHero schema violation (unexpected key '{$key}')";
            }
        }

        /* =====================================================
           REQUIRED UI FIELDS
        ===================================================== */
        foreach (['hero_heading', 'hero_subheading', 'is_active'] as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 Contact hero missing '{$field}'";
            }
        }

        /* =====================================================
           TYPE VALIDATION
        ===================================================== */
        if (!is_string($payload['hero_heading']) || trim($payload['hero_heading']) === '') {
            return "DC-05 ContactHero semantic corruption (invalid hero_heading)";
        }

        if (!is_string($payload['hero_subheading'])) {
            return "DC-05 ContactHero semantic corruption (invalid hero_subheading)";
        }

        /* =====================================================
           OPTIONAL LOTTIE VALIDATION
        ===================================================== */
        if (
            array_key_exists('hero_lottie_url', $payload) &&
            !is_string($payload['hero_lottie_url'])
        ) {
            return "DC-05 ContactHero semantic violation (hero_lottie_url not string)";
        }

        /* =====================================================
           ACTIVE FLAG
        ===================================================== */
        if (!in_array($payload['is_active'], [0, 1], true)) {
            return "DC-05 ContactHero semantic violation (invalid is_active flag)";
        }

        /* =====================================================
           STRICT NO-HTML POLICY
        ===================================================== */
        if ($payload['hero_heading'] !== strip_tags($payload['hero_heading'])) {
            return "DC-05 Contact hero hero_heading must be plain text";
        }

        if ($payload['hero_subheading'] !== strip_tags($payload['hero_subheading'])) {
            return "DC-05 Contact hero hero_subheading must be plain text";
        }

        /* =====================================================
           TRUST ACCEPTED
        ===================================================== */
        return null;
    }
}
