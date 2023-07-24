<?php

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\Category;
use App\RequestValidators\CreateCategoryRequestValidator;
use App\RequestValidators\UpdateCategoryRequestValidator;
use App\ResponseFormatter;
use App\Services\CategoryService;
use App\Services\RequestService;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CategoryController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly CategoryService $categoryService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly RequestService $requestService,
    )
    {
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'categories/index.twig'
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

        return $response;
    }

    // function get

    /**
     * @throws NotSupported
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

    // function update

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $this->requestValidatorFactory
            ->make(UpdateCategoryRequestValidator::class)
            ->validate($args + $request->getParsedBody());

        $category = $this->categoryService->getById((int) $data['id']);

        if ($category === null) {
            return $response->withStatus(404);
        }

        $this->categoryService->update($category, $data['name']);

        return $response;
    }

    // function load
    /**
     * @throws NotSupported
     */
    public function load(Request $request, Response $response): Response
    {
        $params = $this->requestService->getDataTableQueryParams($request);

        $categories = $this->categoryService->getPaginatedCategories($params);
        $formatter = static function (Category $category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'createdAt' => $category->getCreatedAt()->format('m/d/Y g:i A'),
                'updatedAt' => $category->getUpdatedAt()->format('m/d/Y g:i A'),
            ];
        };

        $totalCategories = count($categories);

        return $this->responseFormatter->asDataTable(
            $response,
            array_map($formatter, iterator_to_array($categories)),
            $params->draw,
            $totalCategories
        );
    }
}