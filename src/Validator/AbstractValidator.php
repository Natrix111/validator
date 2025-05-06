<?php

namespace RequestValidator\Validator;

abstract class AbstractValidator implements ValidatorInterface
{
    protected string $field = '';
    protected $value;
    protected array $args = [];
    protected array $messageKeys = [];
    protected string $message = '';

    public function __construct(string $fieldName, $value, array $args = [], string $message = null)
    {
        $this->field = $fieldName;
        $this->value = $value;
        $this->args = $args;
        $this->message = $message ?? $this->message;

        $this->messageKeys = [
            ":value" => $this->value ?? 'null',
            ":field" => $this->field
        ];
    }

    public function validate()
    {
        return $this->rule() ?: $this->messageError();
    }

    private function messageError(): string
    {
        $message = $this->message; // Инициализация перед циклом
        foreach ($this->messageKeys as $key => $value) {
            $message = str_replace($key, (string)$value, $message);
        }
        return $message;
    }

    abstract public function rule(): bool;
}