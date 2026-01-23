<?php
namespace app\CacheValidators\Global;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class HeaderCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return in_array($key, [
            'v1_header_settings',
            'v1_header_navigation'
        ], true);
    }

    public function validate(array $payload): ?string
    {
        /* ================= HEADER SETTINGS ================= */
        if ($this->isHeaderSettings($payload)) {

            $required = [
                'site_title',
                'logo_path',
                'button_text',
                'button_link',
                'is_active'
            ];

            foreach ($required as $key) {
                if (!array_key_exists($key, $payload)) {
                    return "DC-04 Header settings schema missing field '{$key}'";
                }
            }

            if ((int)$payload['is_active'] !== 1) {
                return "DC-05 Header settings semantic violation (inactive config cached)";
            }

            // ---- UNSAFE HTML GUARDS ----
            foreach (['site_title', 'button_text', 'button_link', 'logo_path'] as $field) {
                if ($this->containsHtml($payload[$field])) {
                    return "DC-05 Header settings unsafe HTML detected in '{$field}'";
                }
            }

            return null;
        }

        /* ================= HEADER NAVIGATION ================= */
        foreach ($payload as $index => $item) {

            if (!isset($item['label'], $item['url'])) {
                return "DC-04 Header navigation schema corruption at index {$index}";
            }

            if ($this->containsHtml($item['label']) || $this->containsHtml($item['url'])) {
                return "DC-05 Header navigation unsafe HTML at index {$index}";
            }

            if (trim($item['label']) === '' || trim($item['url']) === '') {
                return "DC-05 Header navigation semantic violation at index {$index}";
            }
        }

        return null; // VALID
    }

    /* =====================================================
       HELPERS
    ===================================================== */

    private function isHeaderSettings(array $payload): bool
    {
        return isset($payload['site_title']);
    }

    private function containsHtml(string $value): bool
    {
        return $value !== strip_tags($value);
    }
}
