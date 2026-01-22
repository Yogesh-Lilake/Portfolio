<?php
namespace app\CacheValidators;

class AboutContentSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about_content';
    }

    public function validate(array $payload): ?string
    {
        $required = [
            'greeting_title',
            'main_description',
            'secondary_description',
        ];

        /* =====================================================
           REQUIRED FIELDS
           ===================================================== */
        foreach ($required as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 About content missing '{$field}'";
            }
        }

        /* =====================================================
           TYPE VALIDATION (MUST COME FIRST)
           ===================================================== */
        if (!is_string($payload['greeting_title'])) {
            return "DC-05 About content greeting_title invalid type";
        }

        if (!is_string($payload['main_description'])) {
            return "DC-05 About content main_description invalid type";
        }

        if (!is_string($payload['secondary_description'])) {
            return "DC-05 About content secondary_description invalid type";
        }

        /* =====================================================
           PLAIN-TEXT CONTENT POLICY
           ===================================================== */
        if (!$this->isPlainText($payload['greeting_title'])) {
            return "DC-05 About content greeting_title must be plain text";
        }

        if (!$this->isPlainText($payload['main_description'])) {
            return "DC-05 About content main_description must be plain text";
        }

        if (!$this->isPlainText($payload['secondary_description'])) {
            return "DC-05 About content secondary_description must be plain text";
        }

        return null;
    }

    private function isPlainText(string $value): bool
    {
        return $value === strip_tags($value);
    }
}
