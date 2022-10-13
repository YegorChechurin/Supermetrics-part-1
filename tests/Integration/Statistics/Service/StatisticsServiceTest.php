<?php

declare(strict_types=1);

namespace Tests\Integration\Statistics\Service;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SocialPost\Client\FictionalClient;
use SocialPost\Driver\FictionalDriver;
use SocialPost\Dto\FetchParamsTo;
use SocialPost\Hydrator\FictionalPostHydrator;
use SocialPost\Service\SocialPostService;
use Statistics\Builder\ParamsBuilder;
use Statistics\Dto\ParamsTo;
use Statistics\Enum\StatsEnum;
use Statistics\Service\Factory\StatisticsServiceFactory;
use Statistics\Service\StatisticsService;
use Traversable;

final class StatisticsServiceTest extends TestCase
{
    private StatisticsService $statisticsService;

    public function setUp(): void
    {
        $this->statisticsService = StatisticsServiceFactory::create();
    }

    public function testAveragePostNumberPerUser(): void
    {
        $allStats = $this->statisticsService->calculateStats(
            $this->getPosts(),
            $this->getReportStatsParams(new DateTime('2018-08-10'))
        );

        $allStatsIndexedByName = [];
        foreach ($allStats->getChildren() as $stats) {
            $allStatsIndexedByName[$stats->getName()] = $stats;
        }

        $this->assertArrayHasKey(StatsEnum::AVERAGE_POST_NUMBER_PER_USER, $allStatsIndexedByName);

        $averagePostNumberPerUser = $allStatsIndexedByName[StatsEnum::AVERAGE_POST_NUMBER_PER_USER];

        $this->assertSame(1.0, $averagePostNumberPerUser->getValue());
        $this->assertSame('posts', $averagePostNumberPerUser->getUnits());
    }

    private function getPosts(): Traversable
    {
        $bodyAuthToken = file_get_contents( __DIR__ . '/../../../../data/auth-token-response.json');
        $bodySocialPosts = file_get_contents( __DIR__ . '/../../../../data/social-posts-response.json');
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], $bodyAuthToken),
            new Response(200, ['Content-Type' => 'application/json'], $bodySocialPosts),
        ]);
        $handler = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        $socialClient =  new FictionalClient($httpClient, 'test_client_id');
        $driver = new FictionalDriver($socialClient);
        $hydrator = new FictionalPostHydrator();
        $socialService = new SocialPostService($driver, $hydrator);

        return $socialService->fetchPosts(new FetchParamsTo(1, 1));
    }

    /**
     * @return ParamsTo[]
     */
    private function getReportStatsParams(DateTime $date): array
    {
        return ParamsBuilder::reportStatsParams($date);
    }
}
