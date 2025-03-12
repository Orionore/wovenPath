<?php

namespace App\Twig\Extension;

use App\Enum\StoryEnum;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EnumExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('story_label', [$this, 'formatStoryEnum']),
        ];
    }

    public function formatStoryEnum(string $enumValue): string
    {
        $enum = StoryEnum::tryFrom($enumValue);

        if ($enum === null) {
            return $enumValue;
        }

        return $enum->getLabel($this->translator);
    }
}