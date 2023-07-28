<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegistrationDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function registerUser(RegistrationDTO $registrationDTO): bool|array
    {
        $email = $registrationDTO->getEmail();

        $this->checkUserNotExist($email);

        $user = $this->createUser($email, $registrationDTO->getPassword());

        $validationErrors = $this->checkUserIsValid($this->validator->validate($user));

        if ($validationErrors > 0) {
            return $validationErrors;
        }

        $this->userRepository->save($user, true);

        return true;
    }

    private function createUser(string $email, string $password): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        return $user;
    }

    private function checkUserNotExist(string $email): void
    {
        $user = $this->userRepository->findOneBy([
            'email' => $email
        ]);

        if ($user !== null) {
            throw new InvalidArgumentException("User already exist!");
        }
    }

    private function checkUserIsValid(ConstraintViolationListInterface $errors): array
    {
        $validationErrors = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }
        }
        return $validationErrors;
    }
}
