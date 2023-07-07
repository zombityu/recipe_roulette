<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeTypeController extends AbstractController
{
    #[Route('/api/recipe-type', name: 'add_recipe_type', methods: 'POST')]
    public function getRecipeType(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RecipeTypeController.php',
        ]);
    }
}
