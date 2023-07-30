<?php

namespace App\Services;

use App\Entity\Receipt;
use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class ReceiptService
{
    public function __construct(
        private readonly EntityManager $entityManager
    )
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function create(Transaction $transaction, string $filename, string $storageFilename): Receipt
    {
        $receipt = new Receipt();
        $receipt->setTransaction($transaction);
        $receipt->setFilename($filename);
        $receipt->setStorageFilename($storageFilename);
        $receipt->setCreatedAt(new DateTime());

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return $receipt;
    }
}