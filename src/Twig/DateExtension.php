<?php

namespace App\Twig;

use App\Service\DateService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class DateExtension extends AbstractExtension implements GlobalsInterface{
    public function __construct(
        private DateService $yearService
    ) {}

    public function getGlobals(): array
    {
        return [
            'year' => $this->yearService->getCurrentYear(),
            'month' => $this->yearService->getCurrentMonth(),
        ];
    }
}