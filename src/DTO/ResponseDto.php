<?php

declare(strict_types=1);

namespace App\DTO;

class ResponseDto
{
    private readonly bool $success;
    private readonly string $message;
    private readonly array $errors;

    public function __construct(bool $success, string $message, array $errors)
    {
        $this->success = $success;
        $this->message = $message;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }


}