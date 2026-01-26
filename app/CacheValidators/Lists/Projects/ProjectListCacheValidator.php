<?php
namespace app\CacheValidators\Lists\Projects;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class ProjectListCacheValidator implements CacheValidatorInterface
{
    /**
     * This validator owns ONLY projects_list cache
     */
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_projects_list_');
    }

    /**
     * Validate list-level cache payload
     *
     * @param array $payload
     * @return string|null  Error code OR null if trusted
     */
    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. CLOSED SCHEMA (LIST LEVEL)
         * ===================================================== */
        $allowedKeys = [
            'items',
            'page',
            'totalPages',
            'total',
            'filters'
        ];

        foreach ($payload as $key => $_) {
            if (!in_array($key, $allowedKeys, true)) {
                return "DC-04 ProjectsList schema violation (unexpected key '{$key}')";
            }
        }

        foreach ($allowedKeys as $key) {
            if (!array_key_exists($key, $payload)) {
                return "DC-04 ProjectsList missing '{$key}'";
            }
        }

        /* =====================================================
         * 2. ITEMS CONTRACT
         * ===================================================== */
        if (!is_array($payload['items'])) {
            return "DC-04 ProjectsList items must be list";
        }

        foreach ($payload['items'] as $i => $item) {
            if (!is_array($item)) {
                return "DC-04 ProjectsList item #{$i} invalid structure";
            }

            $required = [
                'id',
                'title',
                'slug',
                'description',
                'image_path',
                'sort_order',
                'is_featured',
                'is_active'
            ];

            foreach ($required as $field) {
                if (!array_key_exists($field, $item)) {
                    return "DC-04 ProjectsList item #{$i} missing '{$field}'";
                }
            }

            if (!is_int($item['id'])) {
                return "DC-05 ProjectsList item #{$i} id must be integer";
            }

            if (!is_string($item['title']) || $item['title'] === '') {
                return "DC-05 ProjectsList item #{$i} title invalid";
            }

            if (!is_string($item['slug']) || $item['slug'] === '') {
                return "DC-05 ProjectsList item #{$i} slug invalid";
            }
        }

        /* =====================================================
         * 3. PAGINATION INTEGRITY
         * ===================================================== */
        if (!is_int($payload['page']) || $payload['page'] < 1) {
            return "DC-05 ProjectsList page invalid";
        }

        if (!is_int($payload['totalPages']) || $payload['totalPages'] < 1) {
            return "DC-05 ProjectsList totalPages invalid";
        }

        if (!is_int($payload['total']) || $payload['total'] < 0) {
            return "DC-05 ProjectsList total invalid";
        }

        /* =====================================================
         * 4. FILTER CONTRACT
         * ===================================================== */
        if (!is_array($payload['filters'])) {
            return "DC-04 ProjectsList filters must be object";
        }

        if (!array_key_exists('tech', $payload['filters'])
            || !array_key_exists('featured', $payload['filters'])) {
            return "DC-04 ProjectsList filters incomplete";
        }

        if (!is_bool($payload['filters']['featured'])) {
            return "DC-05 ProjectsList filters.featured must be boolean";
        }

        /* =====================================================
         * 5. CONSISTENCY CHECK (OPTIONAL BUT IMPORTANT)
         * ===================================================== */
        if (!empty($payload['items']) && $payload['total'] < count($payload['items'])) {
            return "DC-05 ProjectsList total less than items count";
        }

        return null; // CACHE TRUSTED
    }
}
