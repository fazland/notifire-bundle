<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests;

use Fazland\Notifire\Handler\CompositeNotificationHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * @runTestsInSeparateProcesses
 */
class NotifireBundleCompositeTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel('test', true, 'config_composite.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/Fixtures/cache');
        $fs->remove(__DIR__.'/Fixtures/logs');
    }

    public function testCompositeConfiguration()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $service = $container->get('email_service_holder')->getInstance();

        self::assertNotEmpty($service);
        self::assertInstanceOf(CompositeNotificationHandler::class, $service);
    }
}
