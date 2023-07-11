<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RecipeRequestDTO;
use App\DTO\RecipeResponseDTO;
use App\Entity\Recipe;
use App\Entity\RecipeType;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

class RecipeService implements RecipeServiceInterface
{
    private readonly RecipeRepository $receiptRepository;
    private readonly RecipeTypeRepository $receiptTypeRepository;
    private readonly UserRepository $userRepository;

    public function __construct(RecipeRepository $receiptRepository, RecipeTypeRepository $receiptTypeRepository, UserRepository $userRepository)
    {
        $this->receiptRepository = $receiptRepository;
        $this->receiptTypeRepository = $receiptTypeRepository;
        $this->userRepository = $userRepository;
    }

    public function save(RecipeRequestDTO $receiptDTO, UserInterface $user): void
    {
        $name = $receiptDTO->getName();

        $receipt = $this->receiptRepository->findOneBy([
            'name' => $name
        ]);

        if ($receipt !== null) {
            throw new InvalidArgumentException('Recipe already exist!');
        }

        $type = $this->receiptTypeRepository->find($receiptDTO->getTypeId());

        if ($type === null) {
            throw new InvalidArgumentException("Recipe Type does not exist!");
        }

        $user = $this->userRepository->findOneBy([
            'email' => $user->getUserIdentifier(),
        ]);

        $recipe = $this->createRecipe($name, $receiptDTO, $type, $user);

        $this->receiptRepository->save($recipe, true);
    }


    private function createRecipe(string $name, RecipeRequestDTO $receiptDTO, RecipeType $type, User $user): Recipe
    {
        $receipt = new Recipe();
        $receipt->setName($name);
        $receipt->setPhoto($receiptDTO->getPhoto());
        $receipt->setDescription($receiptDTO->getDescription());
        $receipt->setType($type);
        $receipt->setUser($user);

        return $receipt;
    }

    public function getAllRecipes(UserInterface $user): array
    {
        $recipes = $this->receiptRepository->findAllRecipe($user);

        return $this->createRecipeResponseArray($recipes);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getRecipe(UserInterface $user, string $recipe): RecipeResponseDTO
    {
        $recipe = $this->receiptRepository->findOneByRecipe($user, $recipe);

        if ($recipe === null) {
            throw new InvalidArgumentException('This recipe does not exist!');
        }

        return $this->getRecipeResponseDTO($recipe);
    }


    private function getRecipeResponseDTO(Recipe $recipe): RecipeResponseDTO
    {
        return new RecipeResponseDTO(
            $recipe->getName(),
            $recipe->getPhoto(),
            $recipe->getDescription(),
            $recipe->getType()->getName()
        );
    }

    private function createRecipeResponseArray(array $recipes): array
    {
        $recipeResponse = [];

        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                $recipeResponse[] = $this->getRecipeResponseDTO($recipe);
            }
        }

        return $recipeResponse;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function deleteRecipe(UserInterface $user, string $recipeName): void
    {
        $recipe = $this->receiptRepository->findOneByRecipe($user, $recipeName);
        $this->receiptRepository->remove($recipe, true);
    }
}