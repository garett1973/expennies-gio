<?php

namespace App\RequestValidators;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\RequestValidatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RequestValidatorFactory implements RequestValidatorFactoryInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function make(string $class): RequestValidatorInterface
    {
        $validator = $this->container->get($class);

        if ($validator instanceof RequestValidatorInterface) {
            return $validator;
        }

        throw new \RuntimeException('Invalid validator');
    }
}