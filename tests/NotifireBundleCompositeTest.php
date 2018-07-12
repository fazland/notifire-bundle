<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests;

use Fazland\Notifire\Handler\CompositeNotificationHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireBundleCompositeTest extends WebTestCase
{
    protected static function createKernel(array $options = [])
    {
        return new AppKernel('test', true, 'config_composite.xml');
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
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

        $this->assertNotEmpty($service);
        $this->assertInstanceOf(CompositeNotificationHandler::class, $service);
    }
}
