<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\DTOSerializer;
use App\DTO\RecipeRequestDTO;
use App\DTO\ResponseDto;
use App\Service\RecipeServiceInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    private readonly DTOSerializer $dtoSerializer;
    private readonly RecipeServiceInterface $receiptService;

    public function __construct(DTOSerializer $dtoSerializer, RecipeServiceInterface $receiptService)
    {
        $this->dtoSerializer = $dtoSerializer;
        $this->receiptService = $receiptService;
    }

    #[Route('/api/recipes', name: 'get_recipes', methods: 'GET')]
    public function getRecipes(): Response
    {
        $user = $this->getUser();

        $recipes = $this->receiptService->getAllRecipes($user);

        return $this->json($recipes)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    #[Route('/api/recipe/{recipeName}', name: 'get_recipe', methods: 'GET')]
    public function getRecipe(string $recipeName): Response
    {
        $user = $this->getUser();

        try {
            $recipeDto = $this->receiptService->getRecipe($user, $recipeName);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json($recipeDto, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    #[Route('/api/recipe', name: 'add_recipe', methods: 'POST')]
    public function addRecipe(Request $request): Response
    {
        $receiptDTO = $this->dtoSerializer->deserialize(
            $request->getContent(),
            RecipeRequestDTO::class,
            'json'
        );

        $user = $this->getUser();

        try {
            $this->receiptService->save($receiptDTO, $user);
        } catch (InvalidArgumentException $e) {
            return $this->json($e->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json(
            new ResponseDto(true, 'Successful saving of the recipe', []),
            Response::HTTP_CREATED
        );
    }

    #[Route('/api/recipe/{recipeName}', name: 'delete_recipe', methods: 'DELETE')]
    public function deleteRecipe(string $recipeName): Response
    {
        $user = $this->getUser();

        $this->receiptService->deleteRecipe($user, $recipeName);

        return $this->json(new ResponseDto(true, 'Successful deleted!', []));
    }
}
