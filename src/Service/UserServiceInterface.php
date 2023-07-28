<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegistrationDTO;

interface UserServiceInterface
{
    public function registerUser(RegistrationDTO $registrationDTO): bool|array;
}
