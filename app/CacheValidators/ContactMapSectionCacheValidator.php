<?php
namespace app\CacheValidators;

class ContactMapSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_map';
    }

    public function validate(array $payload): ?string
    {

        $allowedKeys = [
            'id',
            'map_embed_url',
            'is_active',
            'updated_at',
        ];

        /* =====================================================
           CLOSED SCHEMA
        ===================================================== */
        foreach ($payload as $key => $_) {
            if (!in_array($key, $allowedKeys, true)) {
                return "DC-04 ContactMap schema violation (unexpected key '{$key}')";
            }
        }

        /* =====================================================
           REQUIRED FIELDS
        ===================================================== */
        foreach (['map_embed_url', 'is_active'] as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 ContactMap missing '{$field}'";
            }
        }

        /* =====================================================
           TYPE VALIDATION
        ===================================================== */
        if (
            !is_string($payload['map_embed_url']) ||
            trim($payload['map_embed_url']) === ''
        ) {
            return "DC-05 ContactMap semantic violation (map_embed_url invalid)";
        }

        if (!in_array($payload['is_active'], [0, 1], true)) {
            return "DC-05 ContactMap semantic violation (invalid is_active)";
        }

        /* =====================================================
           STRICT NO-HTML POLICY
        ===================================================== */
        if ($payload['map_embed_url'] !== strip_tags($payload['map_embed_url'])) {
            return "DC-05 ContactMap policy violation (HTML not allowed)";
        }

        return null;
    }
}
