<?php

declare(strict_types=1);

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

final class AveragePostNumberPerUser extends AbstractCalculator
{
    protected const UNITS = 'posts';

    private array $authorInventory = [];

    private int $authorsCount = 0;

    private int $totalPosts = 0;

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        ++$this->totalPosts;

        if (array_key_exists($postTo->getAuthorId(), $this->authorInventory)) {
            return;
        }

        ++$this->authorsCount;
        $this->authorInventory[$postTo->getAuthorId()] = null;
    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        if (0 === $this->totalPosts) {
            return (new StatisticsTo())
                ->setUnits(self::UNITS)
                ->setValue(0);
        }

        return (new StatisticsTo())
            ->setUnits(self::UNITS)
            ->setValue(round($this->totalPosts / $this->authorsCount,2));
    }
}
