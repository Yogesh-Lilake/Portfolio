<?php
namespace app\CacheValidators\Pages\Contact;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class ContactToastSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_contact_toast';
    }

    public function validate(array $payload): ?string
    {
        /* =====================================================
           CLOSED SCHEMA
        ===================================================== */
        $allowedKeys = [
            'toast_message',
            'is_active',
            'updated_at',
        ];

        foreach ($payload as $key => $_) {
            if (!in_array($key, $allowedKeys, true)) {
                return "DC-04 ContactToast schema violation (unexpected key '{$key}')";
            }
        }

        /* =====================================================
           REQUIRED FIELDS
        ===================================================== */
        foreach (['toast_message', 'is_active'] as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 ContactToast missing '{$field}'";
            }
        }

        /* =====================================================
           TYPE VALIDATION
        ===================================================== */
        if (
            !is_string($payload['toast_message']) ||
            trim($payload['toast_message']) === ''
        ) {
            return "DC-05 ContactToast semantic violation (toast_message invalid)";
        }

        if (!in_array($payload['is_active'], [0, 1], true)) {
            return "DC-05 ContactToast semantic violation (invalid is_active)";
        }

        /* =====================================================
           STRICT NO-HTML POLICY (JS SAFE)
        ===================================================== */
        if ($payload['toast_message'] !== strip_tags($payload['toast_message'])) {
            return "DC-05 ContactToast policy violation (HTML not allowed)";
        }

        return null;
    }
}
