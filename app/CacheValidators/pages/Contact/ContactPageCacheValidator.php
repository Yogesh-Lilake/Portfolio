<?php
namespace app\CacheValidators\Pages\Contact;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class ContactPageCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_page';
    }

    public function validate(array $payload): ?string
    {
        $sections = ['hero', 'info', 'socials', 'map', 'toast'];

        /* =====================================================
           CLOSED PAGE SCHEMA
        ===================================================== */
        foreach ($payload as $key => $_) {
            if ($key !== 'safe_mode' && !in_array($key, $sections, true)) {
                return "DC-04 ContactPage schema violation (unexpected key '{$key}')";
            }
        }

        foreach (array_merge(['safe_mode'], $sections) as $key) {
            if (!array_key_exists($key, $payload)) {
                return "DC-04 ContactPage missing '{$key}'";
            }
        }

        /* =====================================================
           SAFE MODE
        ===================================================== */
        if (!is_bool($payload['safe_mode'])) {
            return "DC-05 ContactPage safe_mode not boolean";
        }

        if ($payload['safe_mode'] !== false) {
            return "DC-05 ContactPage safe_mode must never be cached as true";
        }

        /* =====================================================
           SECTION CONTRACT
        ===================================================== */
        foreach ($sections as $section) {
            $block = $payload[$section];

            if (!is_array($block)) {
                return "DC-04 ContactPage section '{$section}' invalid structure";
            }

            if (!isset($block['from_db'], $block['data'])) {
                return "DC-04 ContactPage section '{$section}' missing contract keys";
            }

            if (!is_bool($block['from_db'])) {
                return "DC-05 ContactPage {$section}.from_db not boolean";
            }

            if ($block['from_db'] !== true) {
                return "DC-05 ContactPage trust violation (non-DB section '{$section}')";
            }

            if (!is_array($block['data']) || empty($block['data'])) {
                return "DC-05 ContactPage section '{$section}' data empty or invalid";
            }

            /* =====================================================
               DATA SHAPE (VIEW-AWARE)
            ===================================================== */
            if (in_array($section, ['hero', 'map', 'toast'], true) && array_is_list($block['data'])) {
                return "DC-05 ContactPage section '{$section}' must be object";
            }

            if (in_array($section, ['info', 'socials'], true) && !array_is_list($block['data'])) {
                return "DC-05 ContactPage section '{$section}' must be list";
            }
        }

        return null;
    }
}
