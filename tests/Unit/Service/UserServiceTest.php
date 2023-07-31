<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\DTO\RegistrationDTO;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    private RegistrationDTO|MockObject $registrationDTO;
    private UserRepository|MockObject $userRepository;
    private ValidatorInterface|MockObject $validator;
    private UserPasswordHasherInterface|MockObject $passwordHasher;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrationDTO = $this->createMock(RegistrationDTO::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userService = new UserService(
            $this->userRepository,
            $this->passwordHasher,
            $this->validator
        );
    }

    /**
     * @test
     */
    public function registerUser_RegisteredANewUser_RegisteredSuccessful(): void
    {
        $email = 'test@test.com';
        $hashedPassword = 'dgdsdfddfd';
        $user = $this->createTestUser($email, $hashedPassword);

        $errors = new ConstraintViolationList([]);

        $this->registrationDTO
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);

        $this->registrationDTO
            ->expects($this->once())
            ->method('getPassword')
            ->willReturn('test');

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn(null);

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn($hashedPassword);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($user)
            ->willReturn($errors);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user, true);

        $this->assertTrue(
            $this->userService->registerUser($this->registrationDTO)
        );
    }

    /**
     * @test
     */
    public function registerUser_RegisteredUserWithInvalidData_ValidationErrorReturned(): void
    {
        $invalidEmail = 'test';
        $hashedPassword = 'dgdsdfddfd';
        $user = $this->createTestUser($invalidEmail, $hashedPassword);
        $violation = new ConstraintViolation('Violation message', null, [], null, null, null);
        $errors = new ConstraintViolationList([$violation]);

        $this->registrationDTO
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($invalidEmail);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $invalidEmail])
            ->willReturn(null);

        $this->passwordHasher
            ->method('hashPassword')
            ->willReturn($hashedPassword);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($user)
            ->willReturn($errors);

        $this->assertCount(
            1,
            $this->userService->registerUser($this->registrationDTO)
        );
    }

    /**
     * @test
     */
    public function registerUser_RegisteredAnExistingUser_ExceptionReturned(): void
    {
        $email = 'test@test.com';
        $this->registrationDTO
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);

        $this->userRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn($this->createTestUser($email));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User already exist!');

        $this->userService->registerUser($this->registrationDTO);
    }

    private function createTestUser(string $email = '', string $password = ''): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);

        return $user;
    }
}
