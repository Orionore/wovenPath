<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use App\Util\Doctrine\CreatedAtTrait;
use App\Util\Doctrine\UpdatedAtTrait;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Chapter
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Story::class, inversedBy: 'chapters')]
    #[ORM\JoinColumn(name: 'story_id', referencedColumnName: 'id', nullable: false)]
    private ?Story $story = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_chapter_id', referencedColumnName: 'id', nullable: true)]
    private ?self $parentChapter = null;

    #[ORM\OneToMany(mappedBy: 'parentChapter', targetEntity: self::class)]
    private $children;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $position = 1;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): self
    {
        $this->story = $story;

        return $this;
    }

    public function getParentChapter(): ?self
    {
        return $this->parentChapter;
    }

    public function setParentChapter(?self $parentChapter): self
    {
        $this->parentChapter = $parentChapter;

        return $this;
    }

    public function getChildren(): ArrayCollection
    {
        return $this->children;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}