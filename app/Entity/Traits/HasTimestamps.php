<?php

namespace App\Entity\Traits;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

trait HasTimestamps
{
    #[Column(name: 'created_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $createdAt;

    #[Column(name: 'updated_at', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private DateTime $updatedAt;

    #[PrePersist, PreUpdate]
    public function updateTimestamps(): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
        $this->updatedAt = new DateTime();
    }
}