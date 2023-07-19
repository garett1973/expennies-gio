<?php

namespace App\Services;

use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\DataObjects\RegisterUserData;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UserProviderService implements UserProviderServiceInterface
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function getUserById(int $userId): ?UserInterface
    {
        return $this->entityManager->find(User::class, $userId);
    }

    /**
     * @throws NotSupported
     */
    public function getByCredentials(array $credentials): ?UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createUser(RegisterUserData $data): UserInterface
    {
        $user = new User();
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}