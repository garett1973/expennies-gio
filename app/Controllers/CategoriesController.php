<?php

namespace App\Controllers;


use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidators\CreateCategoryRequestValidator;
use App\ResponseFormatter;
use App\Services\CategoryService;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CategoriesController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly CategoryService $categoryService,
        private readonly ResponseFormatter $responseFormatter,
    )
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError|NotSupported
     */
    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'categories/index.twig',
            [
                'categories' => $this->categoryService->getAll(),
            ]
        );
    }

    // function store

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function store(Request $request, Response $response): Response
    {
        // validate request data
        $data = $this->requestValidatorFactory
            ->make(CreateCategoryRequestValidator::class)
        ->validate($request->getParsedBody());
        // create new category
        $this->categoryService->create($data['name'], $request->getAttribute('user'));

        return $response->withHeader('Location', '/categories')->withStatus(302);
    }

    // function delete

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $this->categoryService->delete((int) $args['id']);

        return $response->withHeader('Location', '/categories')->withStatus(302);
    }

    // function get

    /**
     * @throws NotSupported
     * @throws JsonException
     */
    public function get(Request $request, Response $response, array $args): Response
    {
        $category = $this->categoryService->getById((int) $args['id']);

        if ($category === null) {
            return $response->withStatus(404);
        }

        $data = [
            'id' => $category->getId(),
            'name' => $category->getName(),
        ];

        return $this->responseFormatter->asJson($response, $data);
    }
}