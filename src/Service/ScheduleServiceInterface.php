<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RecipeResponseDTO;
use Symfony\Component\Security\Core\User\UserInterface;

interface ScheduleServiceInterface
{
    public function spinTheWheel(UserInterface $user): RecipeResponseDTO;

    public function addRecipeToSchedule(UserInterface $user, string $recipeId): void;

    public function getAllSchedules(UserInterface $user): array;
}