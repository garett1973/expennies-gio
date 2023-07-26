<?php

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\DataObjects\TransactionData;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\TransactionRequiredException;

class TransactionService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(TransactionData $transactionData, User $user): Transaction
    {
        $transaction = new Transaction();
        $transaction->setUser($user);

        return $this->update($transaction, $transactionData);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function update(Transaction $transaction, TransactionData $transactionData): Transaction
    {
        $transaction->setDescription($transactionData->description);
        $transaction->setAmount($transactionData->amount);
        $transaction->setDate($transactionData->date);
        $transaction->setCategory($transactionData->category);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws TransactionRequiredException
     */
    public function delete(int $id): void
    {
        $transaction = $this->entityManager->find(Transaction::class, $id);

        if ($transaction) {
            $this->entityManager->remove($transaction);
            $this->entityManager->flush();
        }
    }

    public function getById(int $id): ?Transaction
    {
        return $this->entityManager->find(Transaction::class, $id);
    }

    /**
     * @throws NotSupported
     */
    public function getPaginatedTransactions(DataTableQueryParams $params): Paginator
    {
        $query = $this->entityManager
            ->getRepository(Transaction::class)
            ->createQueryBuilder('t')
            ->leftJoin('t.category', 'c')
            ->setFirstResult($params->start)
            ->setMaxResults($params->length);

        $orderBy = in_array($params->orderBy, ['description', 'amount', 'date', 'category'])
            ? $params->orderBy
            : 'date';
        $orderDirection = strtolower($params->orderDirection) === 'asc'
            ? 'asc'
            : 'desc';

        if (! empty($params->searchValue)) {
            $query->where('t.description LIKE :search')
                ->setParameter('search', "%" . addcslashes($params->searchValue, '%_') . '%');
        }

        if ($orderBy === 'category') {
            $query->orderBy('c.name', $orderDirection);
        } else {
            $query->orderBy("t.$orderBy", $orderDirection);
        }

        return new Paginator($query);
    }
}