<?php

namespace App;

use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\DataObjects\RegisterUserData;

class Auth implements AuthInterface
{
    private ?UserInterface $user = null;

    public function __construct(
        private readonly UserProviderServiceInterface $userProvider,
        private readonly SessionInterface $session
    )
    {
    }

    public function user(): ?UserInterface
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get('user');

        if (! $userId) {
            return null;
        }

        $user = $this->userProvider->getUserById($userId);

        if (! $user) {
            return null;
        }
        $this->user = $user;

        return $this->user;
    }

    public function attemptLogin(array $credentials): bool
    {
        // check user credentials
        $user = $this->userProvider->getByCredentials($credentials);
        if (! $user || ! $this->checkCredentials($user, $credentials)) {
            return false;
        }

        $this->logIn($user);

        return true;
    }

    public function checkCredentials(UserInterface $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getPassword());
    }

    public function logOut(): void
    {
        $this->session->forget('user');
        $this->session->regenerate();

        $this->user = null;
    }

    public function register(RegisterUserData $data): UserInterface
    {
        $user = $this->userProvider->createUser($data);

        // authenticate user
        $this->logIn($user);

        return $user;
    }

    public function logIn(UserInterface $user): void
    {
        // to avoid session fixation attacks
        $this->session->regenerate();
        // save user_id to session
        $this->session->put('user', $user->getId());

        $this->user = $user;
    }
}