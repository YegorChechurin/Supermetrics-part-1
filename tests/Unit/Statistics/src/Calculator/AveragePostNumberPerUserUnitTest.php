<?php

declare(strict_types=1);

namespace Tests\Unit\Statistics\src\Calculator;

use DateTime;
use SocialPost\Dto\SocialPostTo;
use Statistics\Calculator\AveragePostNumberPerUser;
use Tests\Unit\BaseUnitTestCase;

final class AveragePostNumberPerUserUnitTest extends BaseUnitTestCase
{
    private AveragePostNumberPerUser $calculator;

    public function setUp(): void
    {
        parent::setUp();

        $paramsTo = $this->paramsToFactory->create(
            'test',
            new DateTime('-7 days'),
            new DateTime('+7 days')
        );

        $this->calculator = (new AveragePostNumberPerUser())->setParameters($paramsTo);
    }

    /**
     * @dataProvider improperDatesProvider
     */
    public function testPostsWithImproperDatesAreFilteredOut(DateTime $improperDate): void
    {
        $this->calculator->accumulateData($this->createSocialPostTo('id', $improperDate));

        $this->assertSame(0.0, $this->calculator->calculate()->getValue());
    }

    public function testSameAuthorPostsAreAggregated(): void
    {
        $authorId = 'test_id';
        $posts = [
            $this->createSocialPostTo($authorId, new DateTime()),
            $this->createSocialPostTo($authorId, new DateTime()),
            $this->createSocialPostTo($authorId, new DateTime()),
        ];

        foreach ($posts as $post) {
            $this->calculator->accumulateData($post);
        }

        $this->assertSame((float) count($posts), $this->calculator->calculate()->getValue());
    }

    public function testAveragePostNumberPerUserIsCorrect(): void
    {
        $authorIdOne = '1';
        $authorIdTwo = '2';

        $postToBeFilteredOut = $this->createSocialPostTo($authorIdOne, new DateTime('-1 month'));

        $authorOnePostOne = $this->createSocialPostTo($authorIdOne, new DateTime());
        $authorOnePostTwo = $this->createSocialPostTo($authorIdOne, new DateTime());

        $authorTwoPostOne = $this->createSocialPostTo($authorIdTwo, new DateTime());

        $allPosts = [$postToBeFilteredOut, $authorOnePostOne, $authorOnePostTwo, $authorTwoPostOne];

        foreach ($allPosts as $post) {
            $this->calculator->accumulateData($post);
        }

        $this->assertSame(1.5, $this->calculator->calculate()->getValue());
    }

    public function improperDatesProvider(): array
    {
        return [
            [new DateTime('-14 days')],
            [new DateTime('-10 days')],
            [new DateTime('+10 days')],
            [new DateTime('+14 days')],
        ];
    }

    private function createSocialPostTo(string $authorId, DateTime $postDate): SocialPostTo
    {
        return $this->socialPostToFactory->create(
            'test_id',
            $authorId,
            'test_name',
            'test_text',
            'test_type',
            $postDate
        );
    }
}
