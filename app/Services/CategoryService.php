<?php

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;

class CategoryService
{

    public function __construct(
        private readonly EntityManager $entityManager,
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(string $name, User $user): Category
    {
        $category = new Category();
        $category->setUser($user);

        return $this->update($category, $name);
    }

    /**
     * @throws NotSupported
     */
    public function getAll(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    /**
     * @param DataTableQueryParams $params
     * @return Paginator
     * @throws NotSupported
     */
    public function getPaginatedCategories(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Category::class)
            ->createQueryBuilder('c')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['name', 'createdAt', 'updatedAt']) ? $params->orderBy : 'updatedAt';
        $orderDirection = in_array($params->orderDirection, ['ASC', 'DESC']) ? $params->orderDirection : 'DESC';

        if (! empty($params->searchValue)) {
            $query->where('c.name LIKE :search')
                ->setParameter('search', '%' . addcslashes($params->searchValue, '%_') . '%');
        }

        $query->orderBy('c.' . $orderBy, $orderDirection);
        return new Paginator($query);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function delete(int $id): void
    {
        $category = $this->entityManager->find(Category::class, $id);

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    /**
     * @throws NotSupported
     */
    public function getById(int $id): ?Category
    {
        return $this->entityManager->getRepository(Category::class)->find($id);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(Category $category, mixed $name): Category
    {
        $category->setName($name);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }
}