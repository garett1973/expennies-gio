<?php

namespace App\Entity;

use App\Contracts\UserInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'users')]
#[HasLifecycleCallbacks]
class User implements UserInterface
{
    #[Id, Column(options: ['unsigned' => true]), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(length: 255)]
    private string $name;

    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
        $this->updatedAt = new DateTime();
    }

    #[Column(length: 255, unique: true)]
    private string $email;

    #[Column(length: 255)]
    private string $password;

    #[Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $createdAt;

    #[Column(name: 'updated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $updatedAt;

    #[OneToMany(mappedBy: 'user', targetEntity: Category::class)]
    private Collection $categories;

    #[OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
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
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt(DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * @return User
     */
    public function addCategory(Category $category): User
    {
        $this->categories->add($category);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * @param Transaction $transaction
     * @return User
     */
    public function addTransaction(Transaction $transaction): User
    {
        $this->transactions->add($transaction);
        return $this;
    }
}