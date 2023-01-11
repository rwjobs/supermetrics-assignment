<?php

namespace Tests\unit\Statistics\Calculator;

use DateTime;
use PHPUnit\Framework\TestCase;
use SocialPost\Hydrator\FictionalPostHydrator;
use Statistics\Builder\ParamsBuilder;
use Statistics\Calculator\AveragePostsNumberPerUserPerMonth;
use Traversable;

class AveragePostsNumberPerUserPerMonthTest extends TestCase
{
    /** @test */
    public function testItWillCalculateTheCorrectAverage(): void
    {
        $startDate = new DateTime('2018-08-01T00:00:00+00:00');
        $endDate = new DateTime('2018-08-31T23:59:59+00:00');
        $params = ParamsBuilder::reportStatsParams($startDate, $endDate);

        $calculator = new AveragePostsNumberPerUserPerMonth();
        foreach ($params as $param) {
            $calculator->setParameters($param);
        }

        foreach ($this->getPosts() as $post) {
            $calculator->accumulateData($post);
        }

        $this->assertSame(1.5, $calculator->calculate()->getChildren()[0]->getValue());
    }

    /**
     * @return Traversable
     */
    private function getPosts(): Traversable
    {
        $dummy = json_decode(file_get_contents(__DIR__ . '/../../../data/social-posts-response.json'), true);
        $hydrator = new FictionalPostHydrator();

        foreach ($dummy['data']['posts'] as $post) {
            yield $hydrator->hydrate($post);
        }
    }
}
