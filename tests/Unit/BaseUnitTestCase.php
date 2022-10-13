<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Helpers\ParamsToFactory;
use Tests\Helpers\SocialPostToFactory;

abstract class BaseUnitTestCase extends TestCase
{
    protected ParamsToFactory $paramsToFactory;

    protected SocialPostToFactory $socialPostToFactory;

    public function setUp(): void
    {
        $this->paramsToFactory = new ParamsToFactory();
        $this->socialPostToFactory = new SocialPostToFactory();
    }
}
