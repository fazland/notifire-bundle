<?php

namespace Fazland\NotifireBundle\Tests;

use Fazland\Notifire\Handler\Email\MailgunHandler;
use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\Notifire\Handler\Sms\TwilioHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @author Stefano Rainieri <stefano.rainieri@fazland.com>
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
    public static function tearDownAfterClass()
    {
        $fs = new Filesystem();
        $fs->remove(__DIR__ . '/Fixtures/cache');
        $fs->remove(__DIR__ . '/Fixtures/logs');
    }

    public function provideRoutesAndExpectedResults()
    {
        return [
            ['/test-mailgun-variable-renderer', 'mailgun_variable_render.txt'],
        ];
    }

    public function testSwiftMailerHandlerConfiguration()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        
        $this->assertNotEmpty($container->get("fazland.notifire.handler.swiftmailer.first_mailer"));
        $this->assertInstanceOf(SwiftMailerHandler::class, $container->get("fazland.notifire.handler.swiftmailer.first_mailer"));

        $this->assertNotEmpty($container->get("fazland.notifire.handler.swiftmailer.second_mailer"));
        $this->assertInstanceOf(SwiftMailerHandler::class, $container->get("fazland.notifire.handler.swiftmailer.second_mailer"));
    }

    public function testTwilioServiceHandlerConfiguration()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $name = $container->getParameter("twilio_name");

        $this->assertNotEmpty($container->get("fazland.notifire.handler.twilio.$name"));
        $this->assertInstanceOf(TwilioHandler::class, $container->get("fazland.notifire.handler.twilio.$name"));
    }

    public function testMailgunHandlerConfiguration()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        
        $this->assertNotEmpty($container->get("fazland.notifire.handler.mailgun.example.org"));
        $this->assertInstanceOf(MailgunHandler::class, $container->get("fazland.notifire.handler.mailgun.example.org"));
    }

    /**
     * @dataProvider provideRoutesAndExpectedResults
     */
    public function testMailgunVariableRenderer($route, $resultFile)
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, $route);

        $response = $client->getResponse();
        $this->assertEquals(
            file_get_contents(__DIR__ . '/Fixtures/expected/' . $resultFile), $response->getContent()
        );
    }
}
