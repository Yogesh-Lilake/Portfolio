<?php
namespace app\CacheValidators;

class AboutHeroSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_hero';
    }

    public function validate(array $payload): ?string
    {
        $required = [
            'title',
            'subtitle',
            'animation_url',
            'background_opacity',
            'is_active',
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 About hero missing '{$field}'";
            }
        }

        /* =====================================================
           TYPE VALIDATION
           ===================================================== */

        if (!is_string($payload['title']) || trim($payload['title']) === '') {
            return "DC-05 About hero title invalid";
        }

        if (!is_string($payload['subtitle'])) {
            return "DC-05 About hero subtitle invalid";
        }

        if (!is_numeric($payload['background_opacity'])) {
            return "DC-05 About hero opacity invalid";
        }

        /* =====================================================
           HERO CONTENT POLICY (PLAIN TEXT ONLY)
           ===================================================== */

        if (!$this->isPlainText($payload['title'])) {
            return "DC-05 About hero title must be plain text";
        }

        if (!$this->isPlainText($payload['subtitle'])) {
            return "DC-05 About hero subtitle must be plain text";
        }

        return null;
    }

    /**
     * =====================================================
     * HERO TEXT SANITIZER (BUSINESS RULE)
     * =====================================================
     * Hero content must NEVER contain HTML.
     */
    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
