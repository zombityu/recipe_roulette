<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RecipeResponseDTO;
use App\DTO\ScheduleDTO;
use App\Entity\Recipe;
use App\Entity\Schedule;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

class ScheduleService implements ScheduleServiceInterface
{
    private readonly RecipeRepository $recipeRepository;
    private readonly UserRepository $userRepository;
    private readonly ScheduleRepository $scheduleRepository;

    public function __construct(
        RecipeRepository $recipeRepository,
        UserRepository $userRepository,
        ScheduleRepository $scheduleRepository
    ) {
        $this->recipeRepository = $recipeRepository;
        $this->userRepository = $userRepository;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function spinTheWheel(UserInterface $user): RecipeResponseDTO
    {
        $recipes = $this->recipeRepository->findAllRecipe($user);

        if (empty($recipes)) {
            throw new InvalidArgumentException('Recipe does not exists!');
        }

        $randomRecipes = $this->getRandomRecipes($recipes);

        return $this->getRecipeResponseDTO($randomRecipes);
    }

    private function getRandomRecipes(array $recipes): Recipe
    {
        $randomNumber = rand(0, count($recipes) - 1);

        return $recipes[$randomNumber];
    }

    private function getRecipeResponseDTO(Recipe $randomRecipes): RecipeResponseDTO
    {
        return new RecipeResponseDTO(
            $randomRecipes->getName(),
            $randomRecipes->getPhoto(),
            $randomRecipes->getDescription(),
            $randomRecipes->getType()->getName()
        );
    }

    public function addRecipeToSchedule(UserInterface $user, string $recipeId): void
    {
        $user = $this->userRepository->findOneBy(
            ['email' => $user->getUserIdentifier()]
        );

        $recipe = $this->recipeRepository->findOneBy(
            ['id' => $recipeId],
        );

        if ($recipe === null) {
            throw new InvalidArgumentException('Recipe does not exist!');
        }

        $schedule = $this->createSchedule($recipe, $user);

        $this->scheduleRepository->save($schedule, true);
    }

    private function createSchedule(Recipe $recipe, User $user): Schedule
    {
        $schedule = new Schedule();
        $schedule->setDayOfCooking(new DateTimeImmutable('now'));
        $schedule->setRecipe($recipe);
        $schedule->setUser($user);
        return $schedule;
    }

    /**
     * @return ScheduleDTO[]
     */
    public function getAllSchedules(UserInterface $user): array
    {
        $recipes = $this->scheduleRepository->findAllSchedule($user->getUserIdentifier());

        if (empty($recipes)) {
            throw new InvalidArgumentException('No recipe available!');
        }

        return $this->createScheduleDtoArray($recipes);
    }

    /**
     * @param array $recipes
     * @return array
     */
    public function createScheduleDtoArray(array $recipes): array
    {
        $recipesArray = [];
        foreach ($recipes as $recipe) {
            $recipesArray[] = new ScheduleDTO(
                $recipe->getDayOfCooking(),
                $recipe->getRecipe()->getName(),
                $recipe->getRecipe()->getPhoto(),
                $recipe->getRecipe()->getDescription(),
                $recipe->getRecipe()->getType()->getName()
            );
        }
        return $recipesArray;
    }
}
