<?php

namespace RequestValidator\Requests;

interface RequestInterface
{
    public function rules(): array;
    public function messages(): array;
    public function authorize(): bool;
    public function validated(): array;
}