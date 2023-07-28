<?php

namespace App\Services;

use App\Entity\Receipt;
use App\Entity\Transaction;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReceiptService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function create(Transaction $transaction, string $filename, string $storageFilename): Receipt
    {
        var_dump($transaction, $filename, $storageFilename);
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