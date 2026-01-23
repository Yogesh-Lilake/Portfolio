<?php
namespace app\CacheValidators\Pages\About;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class AboutStatsSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_stats';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
           ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 About stats empty";
        }

        foreach ($payload as $index => $stat) {

            if (!is_array($stat)) {
                return "DC-04 About stat #{$index} invalid structure";
            }

            /* =================================================
               REQUIRED FIELDS
               ================================================= */
            if (!isset($stat['label'], $stat['value'])) {
                return "DC-04 About stat #{$index} missing required fields";
            }

            /* =================================================
               TYPE VALIDATION (FIRST)
               ================================================= */
            if (!is_string($stat['label'])) {
                return "DC-05 About stat #{$index} label invalid type";
            }

            if (!is_string($stat['value'])) {
                return "DC-05 About stat #{$index} value invalid type";
            }

            /* =================================================
               SEMANTIC VALIDATION
               ================================================= */
            if (trim($stat['value']) === '') {
                return "DC-05 About stat #{$index} value empty";
            }

            /* =================================================
               PLAIN-TEXT POLICY (BRAND SAFETY)
               ================================================= */
            if (!$this->isPlainText($stat['label'])) {
                return "DC-05 About stat #{$index} label must be plain text";
            }

            if (!$this->isPlainText($stat['value'])) {
                return "DC-05 About stat #{$index} value must be plain text";
            }
        }

        return null;
    }

    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
