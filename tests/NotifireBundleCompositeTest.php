<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests;

use Fazland\Notifire\Handler\CompositeNotificationHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

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
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new AppKernel('test', true, 'config_composite.xml');
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__.'/Fixtures/cache');
        $fs->remove(__DIR__.'/Fixtures/logs');
    }

    public function testCompositeConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $service = $container->get('email_service_holder')->getInstance();

        self::assertNotEmpty($service);
        self::assertInstanceOf(CompositeNotificationHandler::class, $service);
    }
}
