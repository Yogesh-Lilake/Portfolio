<?php
namespace app\CacheValidators;

class HomeProjectsSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_featured_projects';
    }

    public function validate(array $payload): ?string
    {
        /* ---------- STRUCTURE ---------- */
        if (!is_array($payload)) {
            return "DC-03 Projects section payload corrupted (not array)";
        }

        if (empty($payload)) {
            return "DC-05 Projects section semantic violation (empty projects list)";
        }

        $required = [
            'id',
            'title',
            'slug',
            'description',
            'image_path',
            'project_link',
            'is_featured',
            'is_active'
        ];

        foreach ($payload as $index => $project) {

            if (!is_array($project)) {
                return "DC-03 Projects section item corrupted at index {$index}";
            }

            /* ---------- SCHEMA ---------- */
            foreach ($required as $field) {
                if (!array_key_exists($field, $project)) {
                    return "DC-04 Projects section missing field '{$field}' at index {$index}";
                }
            }

            /* ---------- SEMANTIC ---------- */
            if (trim(strip_tags($project['title'])) === '') {
                return "DC-05 Projects section semantic violation (empty title) at index {$index}";
            }

            if ($project['title'] !== strip_tags($project['title'])) {
                return "DC-05 Projects section semantic violation (unsafe title) at index {$index}";
            }

            if ((int)$project['is_active'] !== 1) {
                return "DC-05 Projects section semantic violation (inactive project cached) at index {$index}";
            }

            if ((int)$project['is_featured'] !== 1) {
                return "DC-05 Projects section semantic violation (non-featured project cached) at index {$index}";
            }
        }

        return null; //  VALID
    }
}
