<?php
namespace app\CacheValidators\Pages\Contact;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class ContactSocialsSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_socials';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           ROOT STRUCTURE
        ===================================================== */
        if (!is_array($payload) || empty($payload)) {
            return "DC-03 ContactSocials payload empty";
        }

        $allowedKeys = [
            'id',
            'platform',
            'icon_class',
            'url',
            'is_active',
            'sort_order',
        ];

        foreach ($payload as $index => $item) {

            if (!is_array($item)) {
                return "DC-03 ContactSocials item {$index} corrupted";
            }

            /* ---------- CLOSED SCHEMA ---------- */
            foreach ($item as $key => $_) {
                if (!in_array($key, $allowedKeys, true)) {
                    return "DC-04 ContactSocials schema violation (unexpected key '{$key}')";
                }
            }

            /* ---------- REQUIRED FIELDS ---------- */
            foreach (['platform', 'icon_class', 'url', 'is_active', 'sort_order'] as $field) {
                if (!array_key_exists($field, $item)) {
                    return "DC-04 ContactSocials missing '{$field}'";
                }
            }

            /* ---------- TYPE VALIDATION ---------- */
            if (
                !is_string($item['platform']) ||
                !is_string($item['icon_class']) ||
                !is_string($item['url']) ||
                !is_int($item['sort_order']) ||
                !in_array($item['is_active'], [0, 1], true)
            ) {
                return "DC-05 ContactSocials semantic violation (invalid field types)";
            }

            /* ---------- STRICT NO-HTML POLICY ---------- */
            foreach (['platform', 'icon_class'] as $textField) {
                if ($item[$textField] !== strip_tags($item[$textField])) {
                    return "DC-05 ContactSocials policy violation (HTML not allowed)";
                }
            }

            /* ---------- URL POLICY ---------- */
            if (!preg_match('#^https?://#i', $item['url'])) {
                return "DC-05 ContactSocials semantic violation (invalid url)";
            }
        }

        return null;
    }
}
