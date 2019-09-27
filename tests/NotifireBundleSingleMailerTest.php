<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests;

use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 *
 * @runTestsInSeparateProcesses
 */
class NotifireBundleSingleMailerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel('test', true, 'config_explicit_mailer.xml');
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

    public function testSwiftMailerHandlerConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        self::assertNotEmpty($container->get('fazland.notifire.handler.email.first_mailer'));
        self::assertInstanceOf(SwiftMailerHandler::class, $container->get('fazland.notifire.handler.email.first_mailer'));

        self::assertFalse($container->has('fazland.notifire.handler.email.second_mailer'));
    }
}
