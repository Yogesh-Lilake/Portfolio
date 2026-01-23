<?php
namespace app\CacheValidators\Pages\Home;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class HomeSkillSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_skills';
    }

    public function validate(array $payload): ?string
    {
        /* ---------- STRUCTURE ---------- */
        if (!is_array($payload)) {
            return "DC-03 Skills section payload corrupted (not array)";
        }

        if (empty($payload)) {
            return "DC-05 Skills section semantic violation (empty skills list)";
        }

        $requiredFields = [
            'skill_name',
            'icon_class',
            'color_class'
        ];

        foreach ($payload as $index => $skill) {

            if (!is_array($skill)) {
                return "DC-03 Skills section item corrupted at index {$index}";
            }

            /* ---------- SCHEMA ---------- */
            foreach ($requiredFields as $field) {
                if (!array_key_exists($field, $skill)) {
                    return "DC-04 Skills section missing field '{$field}' at index {$index}";
                }
            }

            /* ---------- SEMANTIC ---------- */
            if (trim(strip_tags($skill['skill_name'])) === '') {
                return "DC-05 Skills section semantic violation (empty skill_name) at index {$index}";
            }

            if ($skill['skill_name'] !== strip_tags($skill['skill_name'])) {
                return "DC-05 Skills section semantic violation (unsafe skill_name) at index {$index}";
            }

            if (trim($skill['icon_class']) === '' || trim($skill['color_class']) === '') {
                return "DC-05 Skills section semantic violation (empty icon/color) at index {$index}";
            }
        }

        return null; // ✅ VALID
    }
}
