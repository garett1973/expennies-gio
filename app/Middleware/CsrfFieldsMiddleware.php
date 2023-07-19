<?php

namespace App\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class CsrfFieldsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig               $twig,
        private readonly ContainerInterface $container
    )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $csrf = $this->container->get('csrf');

        $csrfNameKey = $csrf->getTokenNameKey();
        $csrfValueKey = $csrf->getTokenValueKey();
        $csrfName = $csrf->getTokenName();
        $csrfValue = $csrf->getTokenValue();

        $csrf = [
            'keys' => [
                'name' => $csrfNameKey,
                'value' => $csrfValueKey
            ],
            'name' => $csrfName,
            'value' => $csrfValue,
            'fields' => <<<CSRF_Fields
                <input type="hidden" name="$csrfNameKey" value="$csrfName" />
                <input type="hidden" name="$csrfValueKey" value="$csrfValue" />
            CSRF_Fields
        ];

        $this->twig->getEnvironment()->addGlobal('csrf', $csrf);

        return $handler->handle($request);
    }
}