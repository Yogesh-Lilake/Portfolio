<?php
namespace app\JsonValidators\Shared;

use app\JsonValidators\Contracts\JsonValidatorInterface;

/**
 * SocialLinksJsonValidator
 *
 * SECURITY PROFILE:
 * - External URLs
 * - HTML class injection risk
 * - Must be STRICTLY validated
 *
 * DC FLOW:
 *  DC-02 → root structure
 *  DC-12 → closed schema
 *  DC-09 → required keys
 *  DC-10 → type + empty
 *  DC-11 → semantic security
 */
class SocialLinksJsonValidator implements JsonValidatorInterface
{
    /**
     * Last validation error code (DC-xx)
     */
    private ?string $errorCode = null;

    /**
     * Closed schema defination
     * Any extra key is rejected
     */
    private array $allowedKeys = [
        'platform',
        'url',
        'icon_class'
    ];

    /**
     * Required footer schema keys
     */
    private array $requiredKeys = [
        'platform',
        'url',
        'icon_class'
    ];

    /**
     * Validator social.json structure + semantic
     */
    public function validate(array $data): bool
    {
        /* ====================================================
         * STEP 1 - ROOT SHAPE CHECK
         * ====================================================
         * Footer social must be an object (associative array)
         */
        if (!array_is_list($data)) {
            $this->errorCode = "DC-02: social.json root must be list";
            return false;
        }

        foreach ($data as $index => $item) {

            /* --------------------------------------------
             * ITEM MUST BE OBJECT
             * -------------------------------------------- */
            if (!is_array($item)) {
                $this->errorCode = "DC-09: social item {$index} not object";
                return false;
            }

            /* --------------------------------------------
             * DC-12 — CLOSED SCHEMA
             * -------------------------------------------- */
            $unknown = array_diff(array_keys($item), $this->allowedKeys);
            if (!empty($unknown)) {
                $this->errorCode =
                    "DC-12: unknown keys in social item {$index}";
                return false;
            }

            /* --------------------------------------------
             * DC-09 — REQUIRED KEYS
             * -------------------------------------------- */
            foreach ($this->requiredKeys as $key) {
                if (!array_key_exists($key, $item)) {
                    $this->errorCode =
                        "DC-09: missing '{$key}' in social item {$index}";
                    return false;
                }
            }

            /* --------------------------------------------
             * DC-10 — TYPE + EMPTY
             * -------------------------------------------- */
            foreach ($this->requiredKeys as $key) {
                if (!isset($item[$key]) || !is_string($item[$key]) || trim($item[$key]) === '') {
                    $this->errorCode =
                        "DC-10: invalid '{$key}' in social item {$index}";
                    return false;
                }
            }

            /* --------------------------------------------
             * DC-11 — SEMANTIC SECURITY
             * -------------------------------------------- */

            // No markup in platform
            if ($this->containsMarkup($item['platform'])) {
                $this->errorCode =
                    "DC-11: unsafe platform in social item {$index}";
                return false;
            }

            // URL must be https:// or mailto:
            if (!$this->isSafeSocialUrl($item['url'])) {
                $this->errorCode =
                    "DC-11: unsafe url in social item {$index}";
                return false;
            }

            // icon_class must be FontAwesome class
            if (!$this->isValidIconClass($item['icon_class'])) {
                $this->errorCode =
                    "DC-11: invalid icon_class in social item {$index}";
                return false;
            }
        }

        return true;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /* ====================================================
     * INTERNAL HELPERS
     * ==================================================== */

    private function containsMarkup(string $value): bool
    {
        return preg_match('/<[^>]*>|javascript:|on\w+=/i', $value);
    }

    private function isSafeSocialUrl(string $url): bool
    {
        if (preg_match('/[<>"\'\(\);]/', $url)) {
            return false;
        }

        return preg_match('#^(https://|mailto:)#i', $url);
    }

    private function isValidIconClass(string $class): bool
    {
        // Only FontAwesome icons allowed
        return preg_match('/^fa[a-z\- ]+$/i', $class);
    }
}
