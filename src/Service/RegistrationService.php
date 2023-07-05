<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegistrationDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationService implements RegistrationServiceInterface
{
    private readonly UserRepository $userRepository;
    private readonly UserPasswordHasherInterface $userPasswordHasher;
    private readonly EntityManagerInterface $entityManager;
    private readonly ValidatorInterface $validator;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function registerUser(RegistrationDTO $registrationDTO): array
    {
        $validationErrors = [];
        $email = $registrationDTO->getEmail();
        $password = $registrationDTO->getPassword();

        $user = $this->userRepository->findOneBy([
            'email' => $email
        ]);

        if($user !== null){
            throw new InvalidArgumentException("User already exist!");
        }

        $user = $this->createUser($email, $password);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }

            return $validationErrors;
        }

        $this->persistUser($user);
        return [];
    }

    private function createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        return $user;
    }

    private function persistUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}