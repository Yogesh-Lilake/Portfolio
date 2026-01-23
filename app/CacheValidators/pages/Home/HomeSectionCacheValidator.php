<?php
namespace app\CacheValidators\Pages\Home;

use app\CacheValidators\Contracts\CacheValidatorInterface;

class HomeSectionCacheValidator implements CacheValidatorInterface
{
    public function supports(string $key): bool
    {
        return $key === 'v1_home';
    }

    public function validate(array $payload): ?string
    {
        $required = [
            'hero_heading',
            'hero_subheading',
            'hero_description',
            'background_image',
            'background_lottie',
            'profile_image',
            'cta_primary_text',
            'cta_primary_link',
            'cta_secondary_text',
            'cta_secondary_link',
            'cv_file_path',
            'seo_title',
            'seo_description',
            'is_active'
        ];

        foreach ($required as $field) {
            if (!array_key_exists($field, $payload)) {
                return "DC-04 Home section missing field '{$field}'";
            }
        }

        /* ===============================
           DC-05: UNSAFE HTML / XSS GUARD
        =============================== */
        if ($this->containsUnsafeHtml($payload['hero_heading'])) {
            return "DC-05 Home section semantic violation (unsafe hero heading)";
        }

        if ($this->containsUnsafeHtml($payload['hero_subheading'])) {
            return "DC-05 Home section semantic violation (unsafe hero subheading)";
        }

        if ($this->containsUnsafeHtml($payload['hero_description'])) {
            return "DC-05 Home section semantic violation (unsafe hero description)";
        }

        return null;
    }

    private function containsUnsafeHtml(string $value): bool
    {
        // Detect script tags, event handlers, JS URLs
        return preg_match(
            '/<\s*script|on\w+\s*=|javascript:/i',
            $value
        ) === 1;
    }
}
