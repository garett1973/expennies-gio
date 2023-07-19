<?php

namespace App\Contracts;

use App\DataObjects\RegisterUserData;

interface UserProviderServiceInterface
{
    public function getByCredentials(array $credentials): ?UserInterface;

    public function getUserById(int $userId): ?UserInterface;

    public function createUser(RegisterUserData $data): UserInterface;
}