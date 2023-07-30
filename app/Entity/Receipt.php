<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'receipts')]
class Receipt
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(name: 'storage_filename', length: 255)]
    private string $storageFilename;

    /**
     * @return string
     */
    public function getStorageFilename(): string
    {
        return $this->storageFilename;
    }

    /**
     * @param string $storageFilename
     * @return Receipt
     */
    public function setStorageFilename(string $storageFilename): Receipt
    {
        $this->storageFilename = $storageFilename;
        return $this;
    }

    #[Column(name: 'filename', length: 255)]
    private string $filename;

    #[Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $createdAt;

    #[ManyToOne(inversedBy: 'receipts')]
    private Transaction $transaction;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Receipt
     */
    public function setFilename(string $filename): Receipt
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return Receipt
     */
    public function setCreatedAt(DateTime $createdAt): Receipt
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

    /**
     * @param Transaction $transaction
     * @return Receipt
     */
    public function setTransaction(Transaction $transaction): Receipt
    {
        $transaction->addReceipt($this);
        $this->transaction = $transaction;
        return $this;
    }
}