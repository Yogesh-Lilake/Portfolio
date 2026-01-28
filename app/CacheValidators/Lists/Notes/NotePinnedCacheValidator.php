<?php
namespace app\CacheValidators\Lists\Notes;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class NotePinnedCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_note_pinned');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. PAYLOAD MUST BE LIST
         * ===================================================== */
        if (!is_array($payload)) {
            return "DC-04 NotePinned payload must be list";
        }

        /* =====================================================
         * 2. CLOSED SCHEMA
         * ===================================================== */
        $allowedKeys = [
            'id',
            'title',
            'slug',
            'description',
            'category_id',
            'is_active',
            'is_pinned',
            'created_at',
            'updated_at',
        ];

        foreach ($payload as $i => $note) {

            if (!is_array($note)) {
                return "DC-04 NotePinned item #{$i} invalid structure";
            }

            // unexpected keys
            foreach ($note as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 NotePinned item #{$i} unexpected key '{$key}'";
                }
            }

            /* =================================================
             * 3. REQUIRED FIELDS
             * ================================================= */
            $required = [
                'id',
                'title',
                'slug',
                'description',
                'is_active',
                'is_pinned',
            ];

            foreach ($required as $field) {
                if (!array_key_exists($field, $note)) {
                    return "DC-04 NotePinned item #{$i} missing '{$field}'";
                }
            }

            /* =================================================
             * 4. TYPE VALIDATION
             * ================================================= */
            if (!is_int($note['id'])) {
                return "DC-05 NotePinned item #{$i} id must be integer";
            }

            if (!is_int($note['is_active'])) {
                return "DC-05 NotePinned item #{$i} is_active must be integer";
            }

            if (!is_int($note['is_pinned']) || $note['is_pinned'] !== 1) {
                return "DC-05 NotePinned item #{$i} is_pinned must be integer(1)";
            }

            /* =================================================
             * 5. STRICT NO-HTML POLICY 
             * ================================================= */
            $htmlFields = [
                'title',
                'slug',
                'description',
            ];

            foreach ($htmlFields as $field) {
                if (!is_string($note[$field])) {
                    return "DC-05 NotePinned item #{$i} {$field} must be string";
                }

                // Detect HTML / script / tags
                if ($note[$field] !== strip_tags($note[$field])) {
                    return "DC-05 NotePinned item #{$i} HTML detected in '{$field}'";
                }
            }
        }

        return null; // CACHE TRUSTED
    }
}
