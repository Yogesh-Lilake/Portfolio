<?php
namespace app\CacheValidators;

class AboutEducationSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_education';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
           ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 About education empty";
        }

        foreach ($payload as $index => $edu) {

            if (!is_array($edu)) {
                return "DC-04 About education #{$index} invalid structure";
            }

            /* =================================================
               REQUIRED FIELDS
               ================================================= */
            if (!isset($edu['degree'], $edu['institution'], $edu['period'])) {
                return "DC-04 About education #{$index} missing required fields";
            }

            /* =================================================
               TYPE VALIDATION (FIRST)
               ================================================= */
            if (!is_string($edu['degree'])) {
                return "DC-05 About education #{$index} degree invalid type";
            }

            if (!is_string($edu['institution'])) {
                return "DC-05 About education #{$index} institution invalid type";
            }

            if (!is_string($edu['period'])) {
                return "DC-05 About education #{$index} period invalid type";
            }

            if (isset($edu['description']) && !is_string($edu['description'])) {
                return "DC-05 About education #{$index} description invalid type";
            }

            /* =================================================
               PLAIN-TEXT POLICY (CREDIBILITY SAFETY)
               ================================================= */
            if (!$this->isPlainText($edu['degree'])) {
                return "DC-05 About education #{$index} degree must be plain text";
            }

            if (!$this->isPlainText($edu['institution'])) {
                return "DC-05 About education #{$index} institution must be plain text";
            }

            if (!$this->isPlainText($edu['period'])) {
                return "DC-05 About education #{$index} period must be plain text";
            }

            if (isset($edu['description']) && !$this->isPlainText($edu['description'])) {
                return "DC-05 About education #{$index} description must be plain text";
            }
        }

        return null;
    }

    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
