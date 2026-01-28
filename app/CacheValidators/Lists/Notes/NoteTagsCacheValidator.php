<?php
namespace app\CacheValidators\Lists\Notes;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class NoteTagsCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_note_tags');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. PAYLOAD MUST BE LIST
         * ===================================================== */
        if (!is_array($payload)) {
            return "DC-04 NoteTags payload must be list";
        }

        /* =====================================================
         * 2. CLOSED SCHEMA
         * ===================================================== */
        $allowedKeys = [
            'id',
            'name',
        ];

        foreach ($payload as $i => $tag) {

            if (!is_array($tag)) {
                return "DC-04 NoteTags item #{$i} invalid structure";
            }

            /* ---------- UNEXPECTED KEYS ---------- */
            foreach ($tag as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 NoteTags item #{$i} unexpected key '{$key}'";
                }
            }

            /* =================================================
             * 3. REQUIRED FIELDS
             * ================================================= */
            foreach ($allowedKeys as $field) {
                if (!array_key_exists($field, $tag)) {
                    return "DC-04 NoteTags item #{$i} missing '{$field}'";
                }
            }

            /* =================================================
             * 4. TYPE VALIDATION
             * ================================================= */
            if (!is_int($tag['id'])) {
                return "DC-05 NoteTags item #{$i} id must be integer";
            }

            if (!is_string($tag['name']) || $tag['name'] === '') {
                return "DC-05 NoteTags item #{$i} name invalid";
            }

            /* =================================================
             * 5. STRICT NO-HTML POLICY 
             * ================================================= */
            if ($tag['name'] !== strip_tags($tag['name'])) {
                return "DC-05 NoteTags item #{$i} HTML detected in 'name'";
            }

            /* =================================================
             * 6. NORMALIZATION GUARD (OPTIONAL BUT STRATEGIC)
             * ================================================= */
            if (mb_strlen($tag['name']) > 32) {
                return "DC-05 NoteTags item #{$i} name too long";
            }
        }

        return null; // CACHE TRUSTED
    }
}
