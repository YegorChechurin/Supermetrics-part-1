<?php

declare(strict_types=1);

namespace Tests\Helpers;

use DateTime;
use Statistics\Dto\ParamsTo;

final class ParamsToFactory
{
    public function create(
        string $statName,
        DateTime $startDate,
        DateTime $endDate
    ): ParamsTo {
        return (new ParamsTo())
            ->setStatName($statName)
            ->setStartDate($startDate)
            ->setEndDate($endDate);
    }
}
