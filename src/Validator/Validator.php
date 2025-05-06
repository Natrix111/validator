<?php

namespace RequestValidator\Validator;

use RequestValidator\Exceptions\ValidationException;

class Validator
{
    private array $validators = [];
    private array $errors = [];
    private array $fields = [];
    private array $rules = [];
    private array $messages = [];

    public function __construct(array $fields, array $rules, array $messages = [])
    {
        $this->validators = app()->settings->app['validators'] ?? [];
        $this->fields = $fields;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $fieldName => $fieldValidators) {
            $this->validateField($fieldName, $fieldValidators);
        }
    }

    private function validateField(string $fieldName, array $fieldValidators): void
    {
        if (!array_key_exists($fieldName, $this->fields)) {
            $this->fields[$fieldName] = null;
        }

        foreach ($fieldValidators as $validatorName) {
            $validatorConfig = $this->parseValidator($validatorName);
            $validatorClass = $this->validators[$validatorConfig['name']] ?? null;

            if (!$validatorClass || !class_exists($validatorClass)) {
                continue;
            }

            $message = $this->getValidatorMessage($fieldName, $validatorConfig['name']);

            $validator = new $validatorClass(
                $fieldName,
                $this->fields[$fieldName],
                $validatorConfig['args'],
                $message
            );

            $result = $validator->validate();
            if ($result !== true) {
                $this->errors[$fieldName][] = $result;
            }
        }
    }

    private function parseValidator(string $validator): array
    {
        $parts = explode(':', $validator, 2);
        return [
            'name' => $parts[0],
            'args' => isset($parts[1]) ? explode(',', $parts[1]) : []
        ];
    }

    private function getValidatorMessage(string $field, string $rule): ?string
    {
        return $this->messages["{$field}.{$rule}"]
            ?? $this->messages[$rule]
            ?? null;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function validateOrFail(): void
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors());
        }
    }
}