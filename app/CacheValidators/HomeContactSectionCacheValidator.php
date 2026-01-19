<?php
namespace app\CacheValidators;

class HomeContactSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact';
    }

    public function validate(array $payload): ?string
    {
        /* ---------- STRUCTURE ---------- */
        if (!is_array($payload)) {
            return "DC-03 Contact section payload corrupted (not object)";
        }

        $required = [
            'title',
            'subtitle',
            'button_text',
            'button_link'
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 Contact section missing field '{$field}'";
            }
        }

        /* ---------- SEMANTIC GUARDS ---------- */

        // Title
        if (trim(strip_tags($payload['title'])) === '') {
            return "DC-05 Contact section semantic violation (empty title)";
        }

        if ($payload['title'] !== strip_tags($payload['title'])) {
            return "DC-05 Contact section semantic violation (unsafe title)";
        }

        // Subtitle
        if (strlen(trim(strip_tags($payload['subtitle']))) < 10) {
            return "DC-05 Contact section semantic violation (subtitle too short)";
        }

        // Button text
        if (trim(strip_tags($payload['button_text'])) === '') {
            return "DC-05 Contact section semantic violation (empty button_text)";
        }

        // Button link (must be internal & safe)
        if (
            !is_string($payload['button_link']) ||
            !preg_match('#^/[a-z0-9/_-]*$#i', $payload['button_link'])
        ) {
            return "DC-05 Contact section semantic violation (unsafe button_link)";
        }

        return null; //  VALID
    }
}
