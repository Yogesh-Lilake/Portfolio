<?php
namespace app\CacheValidators;

class AboutSkillSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_skills';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
           ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 About skills empty";
        }

        foreach ($payload as $index => $skill) {

            if (!is_array($skill)) {
                return "DC-04 About skill #{$index} invalid structure";
            }

            /* =================================================
               REQUIRED FIELDS
               ================================================= */
            if (!isset($skill['skill_name'], $skill['icon_class'])) {
                return "DC-04 About skill #{$index} missing required fields";
            }

            /* =================================================
               TYPE VALIDATION (FIRST)
               ================================================= */
            if (!is_string($skill['skill_name'])) {
                return "DC-05 About skill #{$index} skill_name invalid type";
            }

            if (!is_string($skill['icon_class'])) {
                return "DC-05 About skill #{$index} icon_class invalid type";
            }

            /* =================================================
               PLAIN-TEXT POLICY (BRAND SAFETY)
               ================================================= */
            if (!$this->isPlainText($skill['skill_name'])) {
                return "DC-05 About skill #{$index} skill_name must be plain text";
            }
        }

        return null;
    }

    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
