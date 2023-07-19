<?php

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class RegisterUserRequestValidator implements RequestValidatorInterface
{

    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function validate(array $data): array
    {
        $v = new Validator($data);
        $v->rule('required', ['name', 'email', 'password', 'confirmPassword']);
        $v->rule('email', 'email')->label('Email');
        $v->rule('equals', 'password', 'confirmPassword')->label('Confirm password');
        $v->rule(function($field, $value, $params, $fields) {
            return ! $this->entityManager->getRepository(User::class)->findOneBy(['email' => $value]);
        }, 'email', 'Email already exists');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}