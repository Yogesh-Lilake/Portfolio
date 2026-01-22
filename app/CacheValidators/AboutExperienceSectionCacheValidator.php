<?php
namespace app\CacheValidators;

class AboutExperienceSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_experience';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
           ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 About experience empty";
        }

        foreach ($payload as $index => $exp) {

            if (!is_array($exp)) {
                return "DC-04 About experience #{$index} invalid structure";
            }

            /* =================================================
               REQUIRED FIELDS
               ================================================= */
            if (!isset($exp['title'], $exp['description'])) {
                return "DC-04 About experience #{$index} missing required fields";
            }

            /* =================================================
               TYPE VALIDATION (FIRST)
               ================================================= */
            if (!is_string($exp['title'])) {
                return "DC-05 About experience #{$index} title invalid type";
            }

            if (!is_string($exp['description'])) {
                return "DC-05 About experience #{$index} description invalid type";
            }

            /* =================================================
               PLAIN-TEXT POLICY (BRAND SAFETY)
               ================================================= */
            if (!$this->isPlainText($exp['title'])) {
                return "DC-05 About experience #{$index} title must be plain text";
            }

            if (!$this->isPlainText($exp['description'])) {
                return "DC-05 About experience #{$index} description must be plain text";
            }
        }

        return null;
    }

    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
