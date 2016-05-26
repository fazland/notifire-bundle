<?php

namespace Fazland\NotifireBundle\Tests;

use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireBundleTest extends WebTestCase
{
    protected static function createKernel(array $options = array())
    {
        return new AppKernel('test', true);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__ . '/../Fixtures/Translations/cache');
        $fs->remove(__DIR__ . '/../Fixtures/Translations/logs');
    }

    public function testThisIsATest()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $response = $client->getResponse();
        $this->assertTrue(true);
    }
}
