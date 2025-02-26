<?php
namespace App\Util\Doctrine;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

trait UpdatedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Ignore]
    protected DateTime $updatedAt;

    public function getUpdatedAt(): ?DateTime
    {
        if(!isset($this->updatedAt)) {
            return NULL;
        }
        return $this->updatedAt;
    }

    public function updatedNow(): self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTime();
    }
}
