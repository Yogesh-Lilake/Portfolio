<?php
namespace app\JsonValidators\Pages\Home;

use app\JsonValidators\Contracts\JsonValidatorInterface;

final class HomeAboutSectionJsonValidator implements JsonValidatorInterface
{
    private ?string $errorCode = null;

    private array $schema = [
        'title' => [
            'type'     => 'string',
            'required' => true,
            'min'      => 3,
            'max'      => 100
        ],
        'content' => [
            'type'     => 'string',
            'required' => true,
            'min'      => 20
        ],
        'is_default' => [
            'type'     => 'boolean',
            'required' => false 
        ]
    ];

    public function validate(array $data): bool
    {
        if (empty($data)) {
            $this->errorCode = 'DC-A01: about.json empty';
            return false;
        }

        $allowedKeys = array_keys($this->schema);
        $unknownKeys = array_diff(array_keys($data), $allowedKeys);

        if (!empty($unknownKeys)) {
            $this->errorCode = 
                'DC-A02: unknown keys: ' . implode(', ', $unknownKeys);
            return false;
        }

        foreach ($this->schema as $key => $rules) {
            if ($rules['required'] && !array_key_exists($key, $data)) {
                $this->errorCode = 
                    "DC-A03: missing required keys '{$key}'";
                return false;
            }
        }

        foreach ($this->schema as $key => $rules) {
            if (!isset($data[$key])) {
                continue;
            }

            if (!$this->validateType($data[$key], $rules['type'])) {
                $this->errorCode = 
                    "DC-A04: invalid type for '{$key}'";
                return false;
            }

            if (isset($rules['min']) && mb_strlen(trim($data[$key])) < $rules['min']) {
                $this->errorCode = 
                    "DC-A04: '{$key}' too short";
                return false;
            }

            if (isset($rules['max']) && mb_strlen($data[$key]) > $rules['max']) {
                $this->errorCode = 
                    "DC-A04: '{$key}' exceeds max length";
                return false;
            }
        }

        foreach ($data as $value) {
            if (is_string($value) && $this->containsScript($value)) {
                $this->errorCode = 'DC-A05: XSS detected';
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
        return (bool) preg_match('/<script|javascript:|on\w+=|<iframe/i', $value);
    }
}