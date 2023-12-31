<?php

namespace App\Middleware;

use App\Contracts\SessionInterface;
use App\Exception\SessionException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

class StartSessionsMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly SessionInterface $session)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->session->start();

        $response = $handler->handle($request);

        // TODO: Check for XHR request
        if ($request->getMethod() === 'GET') {
            $this->session->put('previousUrl', (string)$request->getUri());
        }

        $this->session->save();

        return $response;
    }
}