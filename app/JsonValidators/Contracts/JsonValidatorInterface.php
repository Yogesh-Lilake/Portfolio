<?php
namespace app\JsonValidators\Contracts;

interface JsonValidatorInterface
{
    /**
     * Validate decoded JSON data
     * 
     * This method MUST:
     *  - Assume file exists
     *  - Assume JSON syntaxt is valid
     *  - Validate schema + semantics
     * 
     * Return true  => JSON is trusted
     * Return false => JSON rejected (fallback will be used)
     */
    public function validate(array $data): bool;

    /**
     * Return a short failure reason for logging
     * (Used by Models, not by the validator engine)
     */
    public function getErrorCode(): ?string;
}