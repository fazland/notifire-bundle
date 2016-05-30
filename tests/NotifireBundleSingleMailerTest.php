<?php

namespace Fazland\NotifireBundle\Tests;

use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireBundleSingleMailerTest extends WebTestCase
{
    protected static function createKernel(array $options = array())
    {
        return new AppKernel('test', true, 'config_explicit_mailer.xml');
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__ . '/Fixtures/cache');
        $fs->remove(__DIR__ . '/Fixtures/logs');
    }

    public function testSwiftMailerHandlerConfiguration()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->assertNotEmpty($container->get('fazland.notifire.handler.swiftmailer.first_mailer'));
        $this->assertInstanceOf(SwiftMailerHandler::class, $container->get('fazland.notifire.handler.swiftmailer.first_mailer'));

        $this->assertFalse($container->has('fazland.notifire.handler.swiftmailer.second_mailer'));
    }
}
