<?php

namespace App\Service;

class DateService
{
    public function getCurrentYear(): int
    {
        return (int) date('Y');
    }

    public function getFormattedYear(): string
    {
        return date('Y');
    }

    public function getCurrentMonth(): string
    {
        $tableMonth = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', ' Décembre'];

        return $tableMonth[date('m') - 1];
    }
}