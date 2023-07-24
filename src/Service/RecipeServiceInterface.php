<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RecipeRequestDTO;
use App\DTO\RecipeResponseDTO;
use Symfony\Component\Security\Core\User\UserInterface;

interface RecipeServiceInterface
{
    public function save(RecipeRequestDTO $receiptDTO, UserInterface $user): void;
    public function getAllRecipes(UserInterface $user): array;
    public function getRecipe(UserInterface $user, string $recipe): RecipeResponseDTO;

    public function deleteRecipe(UserInterface $user, string $recipeName): void;
}
