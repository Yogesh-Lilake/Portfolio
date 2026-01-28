<?php
namespace app\CacheValidators\Lists\Notes;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class NotesListCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_notes_list');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. PAYLOAD MUST BE LIST
         * ===================================================== */
        if (!is_array($payload)) {
            return "DC-04 NotesList payload must be list";
        }

        /* =====================================================
         * 2. CLOSED SCHEMA — NO EXTRA KEYS
         * ===================================================== */
        $allowedKeys = [
            'id',
            'title',
            'slug',
            'description',
            'category_id',
            'category_name',
            'category_slug',
            'is_active',
            'is_pinned',
            'created_at',
            'updated_at',
        ];

        foreach ($payload as $i => $note) {

            if (!is_array($note)) {
                return "DC-04 NotesList item #{$i} invalid structure";
            }

            /* ---------- UNEXPECTED KEYS ---------- */
            foreach ($note as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 NotesList item #{$i} unexpected key '{$key}'";
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
                'category_name',
                'category_slug',
                'is_active',
            ];

            foreach ($required as $field) {
                if (!array_key_exists($field, $note)) {
                    return "DC-04 NotesList item #{$i} missing '{$field}'";
                }
            }

            /* =================================================
             * 4. TYPE VALIDATION
             * ================================================= */
            if (!is_int($note['id'])) {
                return "DC-05 NotesList item #{$i} id must be integer";
            }

            if (!is_int($note['is_active'])) {
                return "DC-05 NotesList item #{$i} is_active must be integer";
            }

            if (!is_string($note['category_slug']) || $note['category_slug'] === '') {
                return "DC-05 NotesList item #{$i} category_slug invalid";
            }

            /* =================================================
             * 5. STRICT NO-HTML POLICY 
             * ================================================= */
            $htmlFields = [
                'title',
                'slug',
                'description',
                'category_name',
                'category_slug',
            ];

            foreach ($htmlFields as $field) {
                if (!is_string($note[$field])) {
                    return "DC-05 NotesList item #{$i} {$field} must be string";
                }

                //  HTML / SCRIPT / TAG DETECTION
                if ($note[$field] !== strip_tags($note[$field])) {
                    return "DC-05 NotesList item #{$i} HTML detected in '{$field}'";
                }
            }
        }

        return null; // CACHE TRUSTED
    }
}
