<?php
namespace app\CacheValidators\Pages\Notes;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class NotesPageCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_notes_page');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. SAFE MODE
         * ===================================================== */
        if (!isset($payload['safe_mode']) || !is_bool($payload['safe_mode'])) {
            return "DC-05 NotesPage safe_mode must be boolean";
        }

        if ($payload['safe_mode'] !== false) {
            return "DC-05 NotesPage safe_mode must never be cached as true";
        }

        /* =====================================================
         * 2. CLOSED ROOT SCHEMA
         * ===================================================== */
        $allowedKeys = [
            'safe_mode',
            'notes',
            'categories',
            'tags',
            'pinned_notes',
        ];

        foreach ($payload as $key => $_) {
            if (!in_array($key, $allowedKeys, true)) {
                return "DC-04 NotesPage schema violation (unexpected key '{$key}')";
            }
        }

        foreach ($allowedKeys as $key) {
            if (!array_key_exists($key, $payload)) {
                return "DC-04 NotesPage missing key '{$key}'";
            }
        }

        /* =====================================================
         * 3. NOTES LIST (STRICT)
         * ===================================================== */
        if (!is_array($payload['notes'])) {
            return "DC-04 NotesPage notes must be list";
        }

        foreach ($payload['notes'] as $i => $note) {
            if (!is_array($note)) {
                return "DC-04 NotesPage notes item #{$i} invalid structure";
            }

            $required = [
                'id','title','slug','description',
                'category_name','category_slug','is_active'
            ];

            foreach ($required as $field) {
                if (!array_key_exists($field, $note)) {
                    return "DC-04 NotesPage notes item #{$i} missing '{$field}'";
                }
            }

            foreach (['title','slug','description','category_name','category_slug'] as $field) {
                if ($note[$field] !== strip_tags($note[$field])) {
                    return "DC-05 NotesPage notes item #{$i} HTML detected in '{$field}'";
                }
            }
        }

        /* =====================================================
         * 4. CATEGORIES
         * ===================================================== */
        foreach ($payload['categories'] as $i => $cat) {
            if (!is_int($cat['id'])) {
                return "DC-05 NotesPage category #{$i} id must be integer";
            }

            if ($cat['name'] !== strip_tags($cat['name'])) {
                return "DC-05 NotesPage category #{$i} HTML detected in 'name'";
            }

            if ($cat['slug'] !== strtolower($cat['slug'])) {
                return "DC-05 NotesPage category #{$i} slug must be lowercase";
            }
        }

        /* =====================================================
         * 5. TAGS
         * ===================================================== */
        foreach ($payload['tags'] as $i => $tag) {
            if (!is_int($tag['id'])) {
                return "DC-05 NotesPage tag #{$i} id must be integer";
            }

            if ($tag['name'] !== strip_tags($tag['name'])) {
                return "DC-05 NotesPage tag #{$i} HTML detected in 'name'";
            }
        }

        /* =====================================================
         * 6. PINNED NOTES
         * ===================================================== */
        foreach ($payload['pinned_notes'] as $i => $note) {
            if (!is_int($note['is_pinned']) || $note['is_pinned'] !== 1) {
                return "DC-05 NotesPage pinned_notes item #{$i} is_pinned must be integer(1)";
            }

            if ($note['title'] !== strip_tags($note['title'])) {
                return "DC-05 NotesPage pinned_notes item #{$i} HTML detected in 'title'";
            }
        }

        return null; // CACHE TRUSTED
    }
}
