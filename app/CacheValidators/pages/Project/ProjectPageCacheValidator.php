<?php
namespace app\CacheValidators\Pages\Project;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class ProjectPageCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return str_starts_with($key, 'v1_projects_page_');
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
         * 1. SAFE MODE — MUST NEVER BE CACHED
         * ===================================================== */
        if (!isset($payload['safe_mode']) || !is_bool($payload['safe_mode'])) {
            return "DC-05 ProjectsPage safe_mode missing or not boolean";
        }

        if ($payload['safe_mode'] !== false) {
            return "DC-05 ProjectsPage safe_mode must never be cached as true";
        }

        /* =====================================================
         * 2. CLOSED SCHEMA (PAGE LEVEL)
         * ===================================================== */
        $allowedKeys = [
            'safe_mode',
            'projects',
            'techList',
            'page',
            'totalPages',
            'total',
            'filters'
        ];

        foreach ($payload as $k => $_) {
            if (!in_array($k, $allowedKeys, true)) {
                return "DC-04 ProjectsPage schema violation (unexpected key '{$k}')";
            }
        }

        foreach ($allowedKeys as $k) {
            if (!array_key_exists($k, $payload)) {
                return "DC-04 ProjectsPage missing key '{$k}'";
            }
        }

        /* =====================================================
         * 3. PROJECTS LIST CONTRACT
         * ===================================================== */
        if (!is_array($payload['projects'])) {
            return "DC-04 ProjectsPage projects must be list";
        }

        foreach ($payload['projects'] as $i => $project) {
            if (!is_array($project)) {
                return "DC-04 Project item #{$i} invalid structure";
            }

            $required = [
                'id', 'title', 'slug', 'description',
                'image_path', 'sort_order',
                'is_featured', 'is_active'
            ];

            foreach ($required as $field) {
                if (!array_key_exists($field, $project)) {
                    return "DC-04 Project #{$i} missing '{$field}'";
                }
            }

            if (!is_int($project['id'])) {
                return "DC-05 Project id must be integer";
            }

            if (!is_string($project['title']) || $project['title'] === '') {
                return "DC-05 Project title invalid";
            }

            if (!is_string($project['slug']) || $project['slug'] === '') {
                return "DC-05 Project slug invalid";
            }
        }

        /* =====================================================
         * 4. TECH LIST CONTRACT (STRICT MAP)
         * ===================================================== */
        if (!is_array($payload['techList'])) {
            return "DC-04 ProjectsPage techList must be object";
        }

        foreach ($payload['techList'] as $projectId => $techs) {
            if (!is_numeric($projectId) || !is_array($techs)) {
                return "DC-05 ProjectsPage techList invalid index";
            }

            foreach ($techs as $t) {
                if (
                    !isset($t['project_id'], $t['tech_name'], $t['color_class'])
                    || !is_string($t['tech_name'])
                ) {
                    return "DC-05 ProjectsPage techList contract broken";
                }
            }
        }

        /* =====================================================
         * 5. PAGINATION INTEGRITY
         * ===================================================== */
        if (!is_int($payload['page']) || $payload['page'] < 1) {
            return "DC-05 ProjectsPage page invalid";
        }

        if (!is_int($payload['totalPages']) || $payload['totalPages'] < 1) {
            return "DC-05 ProjectsPage totalPages invalid";
        }

        if (!is_int($payload['total']) || $payload['total'] < 0) {
            return "DC-05 ProjectsPage total invalid";
        }

        /* =====================================================
         * 6. FILTER CONTRACT
         * ===================================================== */
        if (!is_array($payload['filters'])) {
            return "DC-04 ProjectsPage filters must be object";
        }

        if (!array_key_exists('tech', $payload['filters'])
            || !array_key_exists('featured', $payload['filters'])) {
            return "DC-04 ProjectsPage filters incomplete";
        }

        if (!is_bool($payload['filters']['featured'])) {
            return "DC-05 ProjectsPage filters.featured must be boolean";
        }

        return null; //  CACHE TRUSTED
    }
}
