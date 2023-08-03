<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ResponseDto;
use App\Service\ScheduleServiceInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    private readonly ScheduleServiceInterface $scheduleService;
    public function __construct(ScheduleServiceInterface $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    #[Route('/', name: 'home_page', methods: 'GET')]
    public function index(): Response
    {
        $user = $this->getUser();


        return $this->json([
            'message' => 'Welcome',
        ]);
    }

    #[Route('/api/spin-the-wheel', name: 'spin_the_wheel', methods: 'GET')]
    public function spinTheWheel(): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return new Response('Unable to access this page! Please log in!', Response::HTTP_FORBIDDEN);
        }

        try {
            $result = $this->scheduleService->spinTheWheel($user);
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json($result, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    #[Route('/api/add-schedule/{recipeId}', name: 'add_recipe_to_schedule', methods: 'POST')]
    public function addRecipeToSchedule(string $recipeId): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return new Response('Unable to access this page! Please log in!', Response::HTTP_FORBIDDEN);
        }

        try {
            $this->scheduleService->addRecipeToSchedule($user, $recipeId);
        } catch (Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ResponseDto(true, 'The recipe has been successfully added.', []));
    }

    #[Route('/api/schedules', name: 'get_all_schedule', methods: 'GET')]
    public function getSchedules(): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            return new Response('Unable to access this page! Please log in!', Response::HTTP_FORBIDDEN);
        }

        try {
            $schedules = $this->scheduleService->getAllSchedules($user);
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->json($schedules, Response::HTTP_CREATED)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
