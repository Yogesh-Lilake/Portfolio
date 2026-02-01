<?php
namespace app\JsonValidators\Pages\Home;

use app\JsonValidators\Contracts\JsonValidatorInterface;

final class HomeContactSectionJsonValidator implements JsonValidatorInterface
{
    private ?string $errorCode = null;

    private array $schema = [
        'title' => [
            'type'     => 'string',
            'min'      => 3,
            'max'      => 100
        ],
        'subtitle' => [
            'type'     => 'string',
            'min' => 5,
            'max'      => 255
        ],
        'button_text' => [
            'type'     => 'string',
            'min'      => 2,
            'max'      => 100
        ],
        'button_link' => [
            'type'     => 'string',
            'min'      => 1,
            'max'      => 255
        ],
        'is_default' => [
            'type' => 'boolean',
            'optional' => true
        ]
    ];

    public function validate(array $data): bool
    {

        if (array_is_list($data)) {
            $this->errorCode = 'DC-C01: contact.json root must be object';
            return false;
        }

        if (empty($data)) {
            $this->errorCode = 'DC-CO1: contact.json empty';
            return false;
        }

        $allowedKeys = array_keys($this->schema);
        $unknownKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($unknownKeys)) {
            $this->errorCode =
                'DC-C02: unknown keys: ' . implode(', ', $unknownKeys);
            return false;
        }

        foreach ($this->schema as $key => $rules) {
            if (!($rules['optional'] ?? false)&& !isset($data[$key])) {
                $this->errorCode = 
                    "DC-CO3: missing required keys '{$key}'";
                return false;
            } 
        }

        foreach ($data as $key => $value) {
            $rules = $this->schema[$key];

            if (!$this->validateType($value, $rules['type'])) {
                $this->errorCode = "DC-C04: invalid type '{$key}'";
                return false;
            }

            if (isset($rules['min']) && mb_strlen($value) < $rules['min']) {
                $this->errorCode = "DC-C05: '{$key}' too short";
                return false;
            }

            if (isset($rules['max']) && mb_strlen($value) > $rules['max']) {
                $this->errorCode = "DC-C06: '{$key}' too long";
                return false;
            }

            if (is_string($value) && $this->containsScript($value)) {
                $this->errorCode = "DC-C07: XSS detected in '{$key}'";
                return false;
            }

            if ($key === 'button_link' && !$this->isSafeLink($value)) {
                $this->errorCode = "DC-C08: invalid button_link";
                return false;
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
            'string'  => is_string($value) && trim($value) !== '',
            'boolean' => is_bool($value),
            default   => false
        };
    }

    private function containsScript(string $value): bool
    {
        return preg_match('/<script|javascript:|on\w+=/i', $value);
    }

    private function isSafeLink(string $link): bool
    {
        return (bool) preg_match(
            '#^(https?://[a-z0-9\.\-]+(:[0-9]+)?(/[a-z0-9/_\-]*)?|[a-z0-9/_\-]+)$#i',
            $link
        );
    }
}