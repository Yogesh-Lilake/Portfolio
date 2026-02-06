<?php
namespace app\JsonValidators\Pages\Home;

use app\JsonValidators\Contracts\JsonValidatorInterface;

/**
 * HomeSectionJsonValidator
 *
 * Unified schema-based validator
 * - Single source of truth
 * - Content-safe
 * - Soft-closed schema
 */
final class HomeSectionJsonValidator implements JsonValidatorInterface
{
    private ?string $errorCode = null;

    /**
     * Unified schema definition
     */
    private array $schema = [
        'hero_heading' => ['type' => 'string', 'required' => true],
        'hero_subheading' => ['type' => 'string', 'required' => true],
        'hero_description' => ['type' => 'string', 'required' => true],

        'background_image' => ['type' => 'string', 'required' => true],
        'background_lottie' => ['type' => 'string', 'required' => true],
        'profile_image' => ['type' => 'string', 'required' => true],

        'cta_primary_text' => ['type' => 'string', 'required' => true],
        'cta_primary_link' => ['type' => 'string', 'required' => true],

        'cta_secondary_text' => ['type' => 'string', 'required' => false],
        'cta_secondary_link' => ['type' => 'string', 'required' => false, 'allowed' => ['DOWNLOAD_CV']],
        'cv_file_path' => ['type' => 'string', 'required' => false],

        'seo_title' => ['type' => 'string', 'required' => true],
        'seo_description' => ['type' => 'string', 'required' => true],

        'is_active' => ['type' => 'integer', 'required' => true],
        'is_default' => ['type' => 'boolean', 'required' => true],
    ];

    public function validate(array $data): bool
    {
        /* ============================================================
         * DC-H01 — Empty JSON
         * ============================================================ */
        if (empty($data)) {
            $this->errorCode = 'DC-H01: home.json empty';
            return false;
        }

        /* ============================================================
         * DC-H02 — Unknown keys (soft-closed)
         * ============================================================ */
        $allowedKeys = array_keys($this->schema);
        $unknownKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($unknownKeys)) {
            $this->errorCode =
                'DC-H02: unknown keys: ' . implode(', ', $unknownKeys);
            return false;
        }

        /* ============================================================
         * DC-H03 — Required keys
         * ============================================================ */
        foreach ($this->schema as $key => $rules) {
            if ($rules['required'] && !array_key_exists($key, $data)) {
                $this->errorCode =
                    "DC-H03: missing required key '{$key}'";
                return false;
            }
        }

        /* ============================================================
         * DC-H04 — Type & empty validation
         * ============================================================ */
        foreach ($data as $key => $value) {
            $type = $this->schema[$key]['type'];

            if (!$this->validateType($value, $type)) {
                $this->errorCode =
                    "DC-H04: invalid type for '{$key}'";
                return false;
            }
        }

        /* ============================================================
         * DC-H05 — Semantic & security validation
         * ============================================================ */

        if (mb_strlen($data['hero_heading']) < 5) {
            $this->errorCode = 'DC-H05: hero_heading too short';
            return false;
        }

        /* ============================================================
         * DC-H06 — CTA SECONDARY ACTION VALIDATION
         * ============================================================ */
        if (
            isset($data['cta_secondary_link']) &&
            isset($this->schema['cta_secondary_link']['allowed']) &&
            !in_array(
                $data['cta_secondary_link'],
                $this->schema['cta_secondary_link']['allowed'],
                true
            )
        ) {
            $this->errorCode = 'DC-H06: invalid cta_secondary_link action';
            return false;
        }

        // LOTTIE: soft check only (no failure, no log)
        if (!$this->isValidUrl($data['background_lottie'])) {
            // silently accepted — normalized later
        }

        foreach (['background_image', 'profile_image', 'cv_file_path'] as $pathKey) {
            if (isset($data[$pathKey]) && !$this->isSafeAssetPath($data[$pathKey])) {
                $this->errorCode =
                    "DC-H07: unsafe path '{$pathKey}'";
                return false;
            }
        }

        foreach ($data as $value) {
            if (is_string($value) && $this->containsScript($value)) {
                $this->errorCode = 'DC-H08: XSS detected';
                return false;
            }
        }

        if (!in_array($data['is_active'], [0, 1], true)) {
            $this->errorCode = 'DC-H09: invalid is_active';
            return false;
        }

        return true;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    /* ================= Helpers ================= */

    private function validateType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string'  => is_string($value) && trim($value) !== '',
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            default   => false
        };
    }

    private function isValidUrl(string $url): bool
    {
        if (preg_match('/[<>"\'\(\);]/', $url)) {
            return false;
        }

        return preg_match('#^(https?://)#i', $url);
    }

    private function isSafeAssetPath(string $path): bool
    {
        return (bool) preg_match(
            '#^(assets|downloads)/[a-zA-Z0-9/_\-.]+\.(jpg|png|webp|svg|pdf)$#',
            $path
        );
    }

    private function containsScript(string $value): bool
    {
        return (bool) preg_match('/<script|javascript:|on\w+=/i', $value);
    }
}
