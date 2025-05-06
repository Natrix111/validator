<?php

namespace RequestValidator\Validator;

interface ValidatorInterface
{
    public function validate(): bool|string;
    public function rule(): bool;
    public function errors(): array;
}