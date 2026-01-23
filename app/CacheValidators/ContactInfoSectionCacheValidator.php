<?php
namespace app\CacheValidators;

class ContactInfoSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_info';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
        ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 ContactInfo payload empty";
        }

        /* =====================================================
           ITEM SCHEMA
        ===================================================== */
        $allowedKeys = [
            'id',
            'type',
            'label',
            'value',
            'icon_class',
            'is_active',
            'sort_order',
        ];

        foreach ($payload as $index => $item) {

            if (!is_array($item)) {
                return "DC-03 ContactInfo item {$index} corrupted";
            }

            /* ---------- CLOSED SCHEMA ---------- */
            foreach ($item as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 ContactInfo schema violation (unexpected key '{$key}')";
                }
            }

            /* ---------- REQUIRED FIELDS ---------- */
            foreach (['type', 'label', 'value', 'icon_class', 'is_active', 'sort_order'] as $field) {
                if (!array_key_exists($field, $item)) {
                    return "DC-04 ContactInfo missing '{$field}'";
                }
            }

            /* ---------- TYPE VALIDATION ---------- */
            if (
                !is_string($item['type']) ||
                !is_string($item['label']) ||
                !is_string($item['value']) ||
                !is_string($item['icon_class']) ||
                !is_int($item['sort_order']) ||
                !in_array($item['is_active'], [0, 1], true)
            ) {
                return "DC-05 ContactInfo semantic violation (invalid field types)";
            }

            /* ---------- STRICT NO-HTML POLICY ---------- */
            foreach (['type', 'label', 'value', 'icon_class'] as $textField) {
                if ($item[$textField] !== strip_tags($item[$textField])) {
                    return "DC-05 ContactInfo policy violation (HTML not allowed)";
                }
            }
        }

        return null;
    }
}
