<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

/**
 * Class Calculator
 *
 * @package Statistics\Calculator
 */
class AveragePostsNumberPerUserPerMonth extends AbstractCalculator
{
    protected const UNITS = 'posts';

    /**
     * @var array
     */
    private array $totals = [];

    /**
     * @param SocialPostTo $postTo
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {
        $date = $postTo->getDate()->format('M, Y');
        $user = $postTo->getAuthorId();

        $this->totals[$date][$user] = ($this->totals[$date][$user] ?? 0) + 1;
    }

    /**
     * @return StatisticsTo
     */
    protected function doCalculate(): StatisticsTo
    {
        $stats = new StatisticsTo();
        foreach ($this->totals as $splitPeriod => $values) {
            $child = (new StatisticsTo())
                ->setName($this->parameters->getStatName())
                ->setSplitPeriod($splitPeriod)
                ->setValue(round(array_sum($values)/ count($values), 2))
                ->setUnits(self::UNITS);

            $stats->addChild($child);
        }

        return $stats;
    }
}
