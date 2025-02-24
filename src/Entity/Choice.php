<?php

namespace App\Entity;

use App\Repository\ChoiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChoiceRepository::class)]
class Choice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: Chapter::class)]
    #[ORM\JoinColumn(name: 'chapter_id', referencedColumnName: 'id', nullable: false)]
    private ?int $chapter_id = null;

    #[ORM\Column(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Chapter::class)]
    #[ORM\JoinColumn(name: 'next_chapter_id', referencedColumnName: 'id', nullable: true)]
    private ?int $next_chapter_id = null;

    #[ORM\Column(length: 255)]
    private ?string $choice_text = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column]
    private ?bool $approved = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChapterId(): ?int
    {
        return $this->chapter_id;
    }

    public function setChapterId(int $chapter_id): static
    {
        $this->chapter_id = $chapter_id;

        return $this;
    }

    public function getNextChapterId(): ?int
    {
        return $this->next_chapter_id;
    }

    public function setNextChapterId(?int $next_chapter_id): static
    {
        $this->next_chapter_id = $next_chapter_id;

        return $this;
    }

    public function getChoiceText(): ?string
    {
        return $this->choice_text;
    }

    public function setChoiceText(string $choice_text): static
    {
        $this->choice_text = $choice_text;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): static
    {
        $this->approved = $approved;

        return $this;
    }
}
