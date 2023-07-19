<?php

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\Contracts\RequestValidatorFactoryInterface;
use App\DataObjects\RegisterUserData;
use App\Exception\ValidationException;
use App\RequestValidators\RegisterUserRequestValidator;
use App\RequestValidators\UserLoginRequestValidator;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Message;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth
    )
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function loginView(Request $request, Response $response): ResponseInterface
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function registerView(Request $request, Response $response): ResponseInterface
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response|Message
    {
        $data = $this->requestValidatorFactory->make(RegisterUserRequestValidator::class)->validate($request->getParsedBody());

        $this->auth->register(
            new RegisterUserData(
                $data['name'],
                $data['email'],
                $data['password']
            )
        );

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logIn(Request $request, Response $response): Response
    {
        // validate request data
        $credentials = $this->requestValidatorFactory->make(UserLoginRequestValidator::class)->validate($request->getParsedBody());

        if (! $this->auth->attemptLogin($credentials)) {
            throw new ValidationException(['password' => ['Invalid email or password']]);
        }

        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function logOut(Request $request, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}