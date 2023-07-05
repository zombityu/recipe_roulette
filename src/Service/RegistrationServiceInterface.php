<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RegistrationDTO;

interface RegistrationServiceInterface
{
    public function registerUser(RegistrationDTO $registrationDTO): array;
}