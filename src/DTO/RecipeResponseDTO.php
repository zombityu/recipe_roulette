<?php

declare(strict_types=1);

namespace App\DTO;

class RecipeResponseDTO
{
    private readonly string $name;
    private readonly string $photo;
    private readonly string $description;
    private readonly string $type;

    public function __construct(string $name, string $photo, string $description, string $type)
    {
        $this->name = $name;
        $this->photo = $photo;
        $this->description = $description;
        $this->type = $type;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }
}