<?php

namespace RequestValidator\Requests;

use RequestValidator\Validator\Validator;
use RequestValidator\Exceptions\ValidationException;

abstract class AbstractRequest
{
    protected array $data;
    protected array $validators = [];

    public function __construct(array $data, array $validators = [])
    {
        $this->data = $data;
        $this->validators = $validators;
    }

    public function validate(): array
    {
        $validator = new Validator(
            $this->data,
            $this->rules(),
            $this->messages(),
            $this->validators
        );

        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }

        return $this->validated();
    }

    protected function validated(): array
    {
        return array_intersect_key($this->data, $this->rules());
    }

    abstract public function rules(): array;

    public function messages(): array
    {
        return [];
    }
}