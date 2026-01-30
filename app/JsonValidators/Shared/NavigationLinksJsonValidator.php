<?php
namespace app\JsonValidators\Shared;

use app\JsonValidators\Contracts\JsonValidatorInterface;

/**
 * NavigationLinksJsonValidator
 * 
 * Validates navigation-style JSON used by:
 *  - Header navigation (navigation.json)
 *  - Footer quick links (links.json)
 * 
 * Responsibility:
 *  - Schema correctness (structure)
 *  - Closed schema enforcement (no unknown keys)
 *  - Type + empty validation
 *  - Semantic safety (internal URLs only)
 * 
 * NOTE:
 *  - JSON syntax (DC-01) and root decodind is habdled in Service layer
 *  - This validator assumes $data is already decoded JSON array 
 */
class NavigationLinksJsonValidator implements JsonValidatorInterface
{
    /**
     * Stores last validation failure code (DC-xx)
     * Used by Service layer for logging
     */
    private ?string $errorCode = null;

    /**
     * Closed schema defination
     * Any extra key = schema drift / potential security risk
     */
    private array $allowedKeys = ['label', 'url'];

    /**
     * Required keys for every navigation item
     */
    private array $requiredKeys = ['label', 'url'];

    /**
     * Validate navigation links JSON payload
     */
    public function validate(array $data): bool
    {
        /** =====================================================
         *  STEP 1 - ROOT SEMANTIC CHECK
         *  =====================================================
         * Navigation JSON must be a LIST of items.
         * (Service layer already checkd json_decode + is_array + array_is_list)
         */

        foreach ($data as $index => $item) {

            /** ====================================================
             *  STEP 2 - ITEM MUST BE OBJECT (ASSOCIATITE ARRAY) (DC-09)
             *  ====================================================
             * Prevents strings, numbers, nulls inside list
             */
            if (!is_array($item)) {
                $this->errorCode = "DC-09: item {$index} not object";
                return false;
            }

            /** ======================================================
             *  STEP 3 - CLOSED SCHEMA (REJECTS UNLNOWN KEYS) (DC-12)
             *  ======================================================
             * Protects against payload drift and hidden injection vectors
             */
            $unknownKeys = array_diff(array_keys($item), $this->allowedKeys);
            if ($unknownKeys) {
                $this->errorCode = "DC-12: unknown keys in nav item {$index}";
                return false;
            }

            /** ========================================================
             *  STEP 4 - REQUIRED KEYS EXUST (DC-09)
             *  ========================================================
             * Checks prsesnce ONLY (value validation comes later) 
             */
            foreach ($this->requiredKeys as $key) {
                if (!array_key_exists($key, $item)) {
                    $this->errorCode = "DC-09: missing '{$key}' in nav item {$index}";
                    return false;
                }
            }

            /** ======================================================
             *  STEP 5 - TYPE + EMPTY VALIDATION (DC-10)
             *  ======================================================
             * At this stage:
             *  - missing
             *  - null
             *  - empty string
             *  - wrong type
             * are all considered INVALID VALUES
             */
            foreach ($this->requiredKeys as $key) {
                if (!isset($item[$key]) || !is_string($item[$key]) || trim($item[$key]) === '') {
                    $this->errorCode = "DC-10: invalid '{$key}' in nav item {$index}";
                    return false;
                }
            }

            /** ========================================================
             *  STEP 6 - SEMANTIC / SECURITY VALIDATION (DC-11)
             *  ========================================================
             * Allow only safe, know internal routes
             */
            if (!$this->isSafeInternalLink($item['url'])) {
                $this->errorCode = "DC-11: unsafe url in nav item {$index}";
                return false;
            }
        }

        /** =======================================================
         *  All items validated successfully
         *  =======================================================
         */
        return true;
    }

    /**
     * Return last validation error code
     * Used by Service layer for logging
     */
    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /**
     * Validate internal navigation URLs
     * 
     * Rules:
     *  - No scripts, HTML, or special characters
     *  - Only allow know internal routes
     */
    private function isSafeInternalLink(string $link): bool
    {
        // Block script-like or injects characters
        if (preg_match('/[<>"\'\(\);]/', $link)) {
            return false;
        }

        // Normalize to "/route"
        $link = '/' . trim($link, '/');

        // Allow-list of valid internal routes
        return in_array($link, [
            '/',
            '/about',
            '/projects',
            '/notes',
            '/contact',
            '/downloadcv'
        ], true);
    }
}