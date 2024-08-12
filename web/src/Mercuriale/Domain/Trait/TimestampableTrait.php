<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Adds created at and updated at timestamps to entities.
 * Entities using this must have HasLifecycleCallbacks annotation.
 *
 * #[ORM\HasLifecycleCallbacks]
 */
trait TimestampableTrait
{
    #[ORM\Column]
    #[Groups('timestampable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups('timestampable')]
    private \DateTimeImmutable $updatedAt;

    /**
     * Gets triggered only on insert.
     */
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Gets triggered every time on update.
     */
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
