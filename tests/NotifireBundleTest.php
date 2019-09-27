<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests;

use Fazland\Notifire\Handler\Email\MailgunHandler;
use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Handler\Sms\SkebbyHandler;
use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
 *
 * @runTestsInSeparateProcesses
 */
class NotifireBundleTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new AppKernel('test', true);
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

    public function provideRoutesAndExpectedResults(): iterable
    {
        yield ['/test-mailgun-variable-renderer', 'mailgun_variable_render.txt'];
    }

    public function testSwiftMailerHandlerConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        self::assertNotEmpty($container->get('fazland.notifire.handler.email.first_mailer'));
        self::assertInstanceOf(SwiftMailerHandler::class, $container->get('fazland.notifire.handler.email.first_mailer'));

        self::assertNotEmpty($container->get('fazland.notifire.handler.email.second_mailer'));
        self::assertInstanceOf(SwiftMailerHandler::class, $container->get('fazland.notifire.handler.email.second_mailer'));
    }

    public function testTwilioServiceHandlerConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $name = $container->getParameter('twilio_name');

        self::assertNotEmpty($container->get("fazland.notifire.handler.sms.$name"));
        self::assertInstanceOf(TwilioHandler::class, $container->get("fazland.notifire.handler.sms.$name"));
    }

    public function testSkebbyHandlerConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        self::assertNotEmpty($container->get('fazland.notifire.handler.sms.skebby'));
        self::assertInstanceOf(SkebbyHandler::class, $container->get('fazland.notifire.handler.sms.skebby'));
    }

    public function testMailgunHandlerConfiguration(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();

        self::assertNotEmpty($container->get('fazland.notifire.handler.email.mailgun_example'));
        self::assertInstanceOf(MailgunHandler::class, $container->get('fazland.notifire.handler.email.mailgun_example'));
    }

    /**
     * @dataProvider provideRoutesAndExpectedResults
     */
    public function testMailgunVariableRenderer(string $route, string $resultFile): void
    {
        $client = static::createClient();
        $client->request('GET', $route);

        $response = $client->getResponse();
        self::assertStringEqualsFile(
            __DIR__.'/Fixtures/expected/'.$resultFile, $response->getContent()
        );
    }
}
