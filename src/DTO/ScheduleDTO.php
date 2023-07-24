<?php

namespace App\DTO;

use DateTimeInterface;

class ScheduleDTO
{
    private readonly DateTimeInterface $dayOfCooking;
    private readonly string $receiptName;
    private readonly string $photo;
    private readonly string $description;
    private readonly string $recipeType;

    public function __construct(
        DateTimeInterface $dayOfCooking,
        string $receiptName,
        string $photo,
        string $description,
        string $recipeType
    ) {
        $this->dayOfCooking = $dayOfCooking;
        $this->receiptName = $receiptName;
        $this->photo = $photo;
        $this->description = $description;
        $this->recipeType = $recipeType;
    }

    public function getDayOfCooking(): DateTimeInterface
    {
        return $this->dayOfCooking;
    }

    public function getReceiptName(): string
    {
        return $this->receiptName;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRecipeType(): string
    {
        return $this->recipeType;
    }
}
