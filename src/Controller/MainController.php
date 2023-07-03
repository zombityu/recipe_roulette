<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): JsonResponse
    {
        return new JsonResponse(["name" => "hello cooking fans"]);
    }
}
