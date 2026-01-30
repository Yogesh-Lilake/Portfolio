<?php
namespace app\JsonValidators\Global;

use app\JsonValidators\Contracts\JsonValidatorInterface;

/**
 * FooterSettingJsonValidator
 * 
 * Validates footer.json defult configuration.
 * 
 * Responsibility:
 *  - Schema presence (DC-09)
 *  - Type + empty validation (DC-10)
 *  - Semantic validation (DC-11)
 *  - Closed schema enforcement (DC-12)
 * 
 * NOTE:
 *  - JSON syntax (DC-01) and root decording are handled in Service layer
 *  - This validator assumes decoded associative array input
 */
class FooterSettingJsonValidator implements JsonValidatorInterface
{
    /**
     * Last validation error code (DC-xx)
     */
    private ?string $errorCode = null;

    /**
     * Required footer schema keys
     */
    private array $requiredKeys = [
        'brand_name',
        'footer_description',
        'developer_name',
        'accent_color'
    ];

    /**
     * Closed schema defination
     * Any extra key is rejected
     */
    private array $allowedKeys = [
        'brand_name',
        'footer_description',
        'developer_name',
        'accent_color'
    ];

    /**
     * Validator footer.json structure + semantic
     */
    public function validate(array $data): bool
    {
        /* ====================================================
         * STEP 1 - ROOT SHAPE CHECK
         * ====================================================
         * Footersettings must be an object (associative array)
         */
        if (array_is_list($data)) {
            $this->errorCode = "DC-02: footer.json root must be object";
            return false;
        }

        /* ====================================================
         * STEP 2 - CLOSED SCHEMA (NO UNKNOWN KEYS)
         * ====================================================
         * Prevents schema drift and injection via extra fields
         */
        $unknownKeys = array_diff(array_keys($data), $this->allowedKeys);
        if (!empty($unknownKeys)) {
            $this->errorCode = "DC-12: footer.json contains unlnown keys";
            return false;
        }

        /* =====================================================
         * STEP 3 - REQUIRED KEYS EXIST (DC-09)
         * =====================================================
         * Presence check ONLY (no value validation here)
         */
        foreach ($this->requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                $this->errorCode =
                    "DC-09: footer.json missing key '{$key}'";
                return false;
            }
        }

        /* ==================================================
         * STEP 4 - TYPE + EMPTY VALIDATION (DC-10)
         * ==================================================
         * Missing, null, empty, or wrong type values are invalid 
         */
        foreach ($this->requiredKeys as $key) {
            if (!isset($data[$key]) || !is_string($data[$key]) || trim($data[$key]) === '') {
                $this->errorCode = "DC-10: footer.json invalid or empty '{$key}'";
                return false;
            }
        }

        /* ==============================================================
         * STEP 5 - REJECT MARKUP / SCRIPT IN ANY FOOTER TEXT (DC-11)
         * ==============================================================
         */
        foreach (['brand_name', 'footer_description', 'developer_name'] as $field) {
            if ($this->containsMarkup($data[$field])) {
                $this->errorCode =
                    "DC-11: footer.json unsafe content in '{$field}'";
                return false;
            }
        }

        /* ===================================================
         * STEP 6 - SEMANTIC VALIDATION (DC-11)
         * ===================================================
         * Validate accent_color is a safe hex value
         */
        if (!$this->isValidHexColor($data['accent_color'])) {
            $this->errorCode = "DC-11: footer.json invalid accent_color";
            return false;
        }

        /* ==============================================
         * Footer JSON is trusted
         * ============================================== 
         */
        return true;
    } 

    /**
     * Return last validatioon error code
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /* =================================================
     * INTERNAL HELPERS
     * =================================================
     */

    /**
     * Validate NO HTML / JS
     */
    private function containsMarkup(string $value): bool
    {
        return preg_match('/<[^>]*>|javascript:|on\w+=/i', $value);
    }

    /**
     * Validate hex color
     * Allowed:
     *  - #RGB
     *  - #RRGGBB
     */
    private function isValidHexColor(string $color): bool
    {
        return (bool) preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $color);
    }
}