<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\DTOSerializer;
use App\DTO\RegistrationDTO;
use App\Service\RegistrationServiceInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private readonly DTOSerializer $dtoSerializer;
    private readonly RegistrationServiceInterface $registrationService;

    public function __construct(DTOSerializer $dtoSerializer, RegistrationServiceInterface $registrationService)
    {
        $this->dtoSerializer = $dtoSerializer;
        $this->registrationService = $registrationService;
    }

    #[Route('/api/registration', name: 'app_registration')]
    public function registration(Request $request): Response
    {
        $data = $this->dtoSerializer->deserialize(
            $request->getContent(),
            RegistrationDTO::class,
            'json'
        );

        try {
            $errorMessages = $this->registrationService->registerUser($data);

            if (!empty($errorMessages)) {
                return $this->json([
                    'success' => false,
                    'message' => 'The provided data is invalid.',
                    'errors' => $errorMessages,
                ], 422);
            }
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json([
            'success' => true,
            'message' => 'Successful registration',
            'errors' => null
        ], Response::HTTP_CREATED);
    }
}
