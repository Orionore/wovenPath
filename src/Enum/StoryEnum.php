<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatorInterface;

enum StoryEnum: string
{
    case DETECTIVE = 'detective';
    case SCIENCE_FICTION = 'science-fiction';
    case FANTASTIC = 'fantastic';
    case FANTASY = 'fantasy';
    case ROMANCE = 'romance';
    case ADVENTURE = 'adventure';
    case HISTORICAL = 'historical';
    case HORROR = 'horror';
    case THRILLER = 'thriller';
    case DRAMA = 'drama';
    case COMEDY = 'comedy';
    case BIOGRAPHY = 'biography';
    case CHILDREN = 'children';
    case DYSTOPIA = 'dystopia';
    case WESTERN = 'western';
    case POETRY = 'poetry';
    case THEATER = 'theater';
    case FAIRYTALE = 'fairytale';
    case MYTHOLOGY = 'mythology';
    case SATIRE = 'satire';
    case MYSTERY = 'mystery';

    /**
     * Return the label translation
     */
    public function getLabel(TranslatorInterface $translator): string
    {
        return $translator->trans('story_enum.' . $this->value, [], 'enums');
    }

    /**
     * Returns all cases with their translated labels
     *
     * @return array<string, string>
     */
    public static function getChoices(TranslatorInterface $translator): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel($translator)] = $case->value;
        }
        return $choices;
    }
}