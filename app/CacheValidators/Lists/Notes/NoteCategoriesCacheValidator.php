<?php
namespace app\CacheValidators\Lists\Notes;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class NoteCategoriesCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_note_categories');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. PAYLOAD MUST BE LIST
         * ===================================================== */
        if (!is_array($payload)) {
            return "DC-04 NoteCategories payload must be list";
        }

        /* =====================================================
         * 2. CLOSED SCHEMA
         * ===================================================== */
        $allowedKeys = [
            'id',
            'name',
            'slug',
            'color_class',
        ];

        foreach ($payload as $i => $cat) {

            if (!is_array($cat)) {
                return "DC-04 NoteCategories item #{$i} invalid structure";
            }

            /* ---------- UNEXPECTED KEYS ---------- */
            foreach ($cat as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 NoteCategories item #{$i} unexpected key '{$key}'";
                }
            }

            /* =================================================
             * 3. REQUIRED FIELDS
             * ================================================= */
            foreach (['id', 'name', 'slug'] as $field) {
                if (!array_key_exists($field, $cat)) {
                    return "DC-04 NoteCategories item #{$i} missing '{$field}'";
                }
            }

            /* =================================================
             * 4. TYPE VALIDATION
             * ================================================= */
            if (!is_int($cat['id'])) {
                return "DC-05 NoteCategories item #{$i} id must be integer";
            }

            if (!is_string($cat['name']) || $cat['name'] === '') {
                return "DC-05 NoteCategories item #{$i} name invalid";
            }

            if (!is_string($cat['slug']) || $cat['slug'] === '') {
                return "DC-05 NoteCategories item #{$i} slug invalid";
            }

            /* =================================================
             * 5. STRICT NO-HTML POLICY 
             * ================================================= */
            if ($cat['name'] !== strip_tags($cat['name'])) {
                return "DC-05 NoteCategories item #{$i} HTML detected in 'name'";
            }

            /* =================================================
             * 6. SLUG POLICY (URL-SAFE)
             * ================================================= */
            if (!preg_match('/^[a-z0-9\-]+$/', $cat['slug'])) {
                return "DC-05 NoteCategories item #{$i} slug invalid";
            }

            if ($cat['slug'] !== strtolower($cat['slug'])) {
                return "DC-05 NoteCategories item #{$i} slug must be lowercase";
            }

            /* =================================================
             * 7. COLOR CLASS (OPTIONAL, BUT SANITIZED)
             * ================================================= */
            if (
                array_key_exists('color_class', $cat)
                && $cat['color_class'] !== null
                && !is_string($cat['color_class'])
            ) {
                return "DC-05 NoteCategories item #{$i} color_class invalid";
            }
        }

        return null; // CACHE TRUSTED
    }
}
