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
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeTypeRepository $recipeTypeRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function save(RecipeRequestDTO $receiptDTO, UserInterface $user): void
    {
        $name = $receiptDTO->getName();

        $this->checkReceiptExist($name);

        $type = $this->getRecipeType($receiptDTO);

        $user = $this->userRepository->findOneBy([
            'email' => $user->getUserIdentifier(),
        ]);

        $recipe = $this->createRecipe($name, $receiptDTO, $type, $user);

        $this->recipeRepository->save($recipe, true);
    }


    private function createRecipe(
        string $name,
        RecipeRequestDTO $receiptDTO,
        RecipeType $type,
        User $user
    ): Recipe {
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
        $recipes = $this->recipeRepository->findAllRecipe($user);

        return $this->createRecipeResponseArray($recipes);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getRecipe(UserInterface $user, string $recipe): RecipeResponseDTO
    {
        $recipe = $this->recipeRepository->findOneByRecipe($user, $recipe);

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
        $recipe = $this->recipeRepository->findOneByRecipe($user, $recipeName);
        $this->recipeRepository->remove($recipe, true);
    }

    private function checkReceiptExist(string $name): void
    {
        $receipt = $this->recipeRepository->findOneBy([
            'name' => $name
        ]);

        if ($receipt !== null) {
            throw new InvalidArgumentException('Recipe already exist!');
        }
    }

    public function getRecipeType(RecipeRequestDTO $receiptDTO): RecipeType
    {
        $type = $this->recipeTypeRepository->find($receiptDTO->getTypeId());

        if ($type === null) {
            throw new InvalidArgumentException("Recipe Type does not exist!");
        }
        return $type;
    }
}
