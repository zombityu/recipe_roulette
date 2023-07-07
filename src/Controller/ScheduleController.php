<?php

namespace App\Controller;

use App\Service\ScheduleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    private readonly ScheduleServiceInterface $scheduleService;
    #[Route('/api/spin-the-wheel', name: 'spin_the_wheel', methods: 'GET')]
    public function spinTheWheel(): Response
    {
        $user = $this->getUser();



        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ScheduleController.php',
        ]);
    }
}
