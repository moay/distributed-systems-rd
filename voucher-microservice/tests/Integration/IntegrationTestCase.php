<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntegrationTestCase extends WebTestCase
{
    protected function setUp()
    {
        exec(dirname(__DIR__, 2).'/bin/console doctrine:migrations:migrate --env=test --no-interaction');
        parent::setUp();
    }
}
