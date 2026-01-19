<?php
namespace app\CacheValidators;

class HomeAboutSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_about';
    }

    public function validate(array $payload): ?string
    {
        /* ===============================
           REQUIRED SCHEMA
        =============================== */
        $required = ['id', 'title', 'content', 'is_active'];

        foreach ($required as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 About section missing field '{$field}'";
            }
        }

        /* ===============================
           TYPE & SEMANTIC RULES
        =============================== */
        if (!is_numeric($payload['id'])) {
            return "DC-05 About section semantic violation (invalid id)";
        }

        if (!is_string($payload['title']) || trim($payload['title']) === '') {
            return "DC-05 About section semantic violation (empty title)";
        }

        if (!is_string($payload['content']) || trim(strip_tags($payload['content'])) === '') {
            return "DC-05 About section semantic violation (empty content)";
        }

        if (!in_array($payload['is_active'], [0, 1, true, false], true)) {
            return "DC-05 About section semantic violation (is_active invalid)";
        }

        /* ===============================
           SECURITY — XSS / UNSAFE HTML
        =============================== */
        if ($this->containsUnsafeHtml($payload['title'])) {
            return "DC-05 About section semantic violation (unsafe title)";
        }

        if ($this->containsUnsafeHtml($payload['content'])) {
            return "DC-05 About section semantic violation (unsafe content)";
        }

        return null; //  VALID ABOUT CACHE
    }

    private function containsUnsafeHtml(string $value): bool
    {
        return preg_match(
            '/<\s*script|on\w+\s*=|javascript:/i',
            $value
        ) === 1;
    }
}
