<?php
namespace app\JsonValidators\Pages\Home;

use app\JsonValidators\Contracts\JsonValidatorInterface;

final class HomeSkillsSectionJsonValidator implements JsonValidatorInterface
{

    private ?string $errorCode = null;

        private array $schema = [
            'skill_name' => [
                'type' => 'string',
                'min'  => 2,
                'max'  => 100
            ],
            'icon_class' => [
                'type' => 'string',
                'min'  => 3,
                'max'  => 100
            ],
            'color_class' => [
                'type' => 'string',
                'min'  => 3,
                'max'  => 50
            ],
            'is_default' => [
                'type' => 'boolean',
                'optional' => true
            ]
        ];

    public function validate(array $data): bool
    {
        if (!array_is_list($data)) {
            $this->errorCode = 'DC-S01: skills.json root must be list';
            return false;
        }

        foreach ($data as $index => $item) {

            if (!is_array($item)) {
                $this->errorCode = "DC-S02: skill[$index] not object";
                return false;
            }

            $allowedKeys = array_keys($this->schema);
            $unknownKeys = array_diff(array_Keys($item), $allowedKeys);

            if (!empty($unknownKeys)) {
                $this->errorCode = 
                    "DC-SO3: skill[$index] unknown keys: " . implode(', ', $unknownKeys);
                return false;
            }

            foreach ($this->schema as $key => $rules) {
                if (!($rules['optional'] ?? false) && !isset($item[$key])) {
                    $this->errorCode = 
                        "DC-S04: skill[$index] missing '{$key}'";
                    return false;
                }
            }

            foreach ($item as $key => $value) {
                $rules = $this->schema[$key];

                if (!$this->validateType($value, $rules['type'])) {
                    $this->errorCode = 
                        "DC-S05: skill[$index] invalid type '{$key}'";
                    return false;
                }

                if (isset($rules['min']) && mb_strlen($value) < $rules['min']) {
                    $this->errorCode = 
                        "DC-S05: skill[$index] '{$key}' too short";
                    return false;
                }

                if (isset($rules['max']) && mb_strlen($value) > $rules['max']) {
                    $this->errorCode = 
                        "DC-S05: skill[$index] '{$key}' too long";
                    return false;
                }

                if (is_string($value) && $this->containsScript($value)) {
                    $this->errorCode = 
                        "DC-S06: skill[$index] XSS detected";
                    return false;
                }

                if ($key === 'icon_class' && !$this->isValidIconClass($value)) {
                    $this->errorCode = 
                        "DC-S07: skill[$index] invalid icon_class";
                    return false;
                }

                if ($key === 'color_class' && !$this->isValidColorClass($value)) {
                    $this->errorCode = 
                        "DC-S08: skill[$index] invalid color_class";
                    return false;
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
            'string'  => is_string($value) && trim($value) !== '',
            'boolean' => is_bool($value),
            default   => false
        };
    }

    private function containsScript(string $value): bool
    {
        return (bool) preg_match('/<script|javascript:|on\w+=/i', $value);
    }

    private function isValidIconClass(string $class): bool
    {
        // Only FontAwesome icons allowed
        return (bool) preg_match('/^fa[a-z0-9\- ]+$/i', $class);
    }

    private function isValidColorClass(string $class): bool
    {
        // Tailwind text color only: text-{color}-{shade}
        return (bool) preg_match(
            '/^text-(slate|gray|zinc|neutral|stone|red|orange|amber|yellow|lime|green|emerald|teal|cyan|sky|blue|indigo|violet|purple|fuchsia|pink|rose)-[1-9]00$/',
            $class
        );
    }
}