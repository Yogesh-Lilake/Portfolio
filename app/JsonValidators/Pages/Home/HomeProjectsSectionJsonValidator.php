<?php
namespace app\JsonValidators\Pages\Home;

use app\JsonValidators\Contracts\JsonValidatorInterface;

final class HomeProjectsSectionJsonValidator implements JsonValidatorInterface
{
    private ?string $errorCode = null;
    private array $seenSortOrders = [];

    private array $schema = [
        'id' => [
            'type' => 'integer',
            'optional' => true
            ],

        'title' => ['type' => 'string', 'min' => 3, 'max' => 150],

        'slug' => ['type' => 'string', 'min' => 3, 'max' => 150],

        'short_desc' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true,
            'max' => 255
        ],

        'description' => ['type' => 'string', 'min' => 10],

        'full_desc' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'image_path' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'cover_image' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'github_url' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'live_url' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'project_link' => [
            'type' => 'string',
            'optional' => true,
            'nullable' => true
        ],

        'is_featured' => ['type' => 'integer'],

        'sort_order' => ['type' => 'integer'],

        'is_default' => [
            'type' => 'boolean',
            'optional' => true
        ],
    ];

    public function validate(array $data): bool
    {
        if (!array_is_list($data)) {
            $this->errorCode = 'DC-P01: projects.json root must be list';
            return false;
        }

        foreach ($data as $i => $project) {

            if (!is_array($project)) {
                $this->errorCode = "DC-P02: project[$i] not object";
                return false;
            }

            $unknown = array_diff(array_keys($project), array_keys($this->schema));
            if ($unknown) {
                $this->errorCode =
                    "DC-P03: project[$i] unknown keys: " . implode(', ', $unknown);
                return false;
            }

            foreach ($this->schema as $key => $rules) {
                if (!($rules['optional'] ?? false) && !array_key_exists($key, $project)) {
                    $this->errorCode =
                        "DC-P04: project[$i] missing '$key'";
                    return false;
                }
            }

            foreach ($project as $key => $value) {
                $rules = $this->schema[$key];

                /* ---------- NULL HANDLING ---------- */
                if ($value === null) {
                    if (!($rules['nullable'] ?? false)) {
                        $this->errorCode =
                            "DC-P05: project[$i] '$key' cannot be null";
                        return false;
                    }
                    continue;
                }

                /* ---------- TYPE ---------- */
                if (!$this->validateType($value, $rules['type'])) {
                    $this->errorCode =
                        "DC-P06: project[$i] invalid type '$key'";
                    return false;
                }

                /* ---------- STRING LIMITS ---------- */
                if (is_string($value)) {
                    if (isset($rules['min']) && mb_strlen($value) < $rules['min']) {
                        $this->errorCode =
                            "DC-P07: project[$i] '$key' too short";
                        return false;
                    }

                    if (isset($rules['max']) && mb_strlen($value) > $rules['max']) {
                        $this->errorCode =
                            "DC-P08: project[$i] '$key' too long";
                        return false;
                    }

                    if ($this->containsScript($value)) {
                        $this->errorCode =
                            "DC-P09: project[$i] XSS detected";
                        return false;
                    }
                }

                /* ---------- LINKS ---------- */
                if (in_array($key, ['github_url', 'live_url', 'project_link'], true)
                    && !$this->isSafeLink($value)) {
                    $this->errorCode =
                        "DC-P10: project[$i] invalid '$key'";
                    return false;
                }

                /* ---------- is_featured ---------- */
                if ($key === 'is_featured' && !in_array($value, [1], true)) {
                    $this->errorCode =
                        "DC-P11: project[$i] is_featured must be 1";
                    return false;
                }

                /* ---------- UNIQUE sort_order ---------- */
                if ($key === 'sort_order') {
                    if (in_array($value, $this->seenSortOrders, true)) {
                        $this->errorCode =
                            "DC-P12: duplicate sort_order '$value'";
                        return false;
                    }
                    $this->seenSortOrders[] = $value;
                }
            }
        }

        return true;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    private function validateType(mixed $value, string $type): bool
    {
        return match ($type) {
            'string'  => is_string($value),
            'integer' => is_int($value),
            'boolean' => is_bool($value),
            default   => false,
        };
    }

    private function containsScript(string $value): bool
    {
        return (bool) preg_match('/<script|javascript:|on\w+=/i', $value);
    }

    private function isSafeLink(string $link): bool
    {
        if ($link === '#' || $link === '') return true;

        return (bool) preg_match(
            '#^(https?://[a-z0-9\.\-]+(:\d+)?(/[a-z0-9/_\-]*)?|[a-z0-9/_\-]+)$#i',
            $link
        );
    }
}
