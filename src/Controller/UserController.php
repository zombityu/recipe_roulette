<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\DTOSerializer;
use App\DTO\RegistrationDTO;
use App\DTO\ResponseDto;
use App\Service\UserServiceInterface;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private readonly DTOSerializer $dtoSerializer;
    private readonly UserServiceInterface $registrationService;

    public function __construct(DTOSerializer $dtoSerializer, UserServiceInterface $registrationService)
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
                return $this->json(
                    new ResponseDto(false, 'The provided data is invalid.', $errorMessages),
                    422
                );
            }
        } catch (InvalidArgumentException $e) {
            return new Response($e->getMessage(), Response::HTTP_CONFLICT);
        }

        return $this->json(
            new ResponseDto(true, 'Successful registration', []),
            Response::HTTP_CREATED
        );
    }
}
