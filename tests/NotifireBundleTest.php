<?php

namespace Fazland\NotifireBundle\Tests;

use Fazland\Notifire\EventSubscriber\Email\MailgunHandler;
use Fazland\Notifire\EventSubscriber\Email\SwiftMailerHandler;
use Fazland\Notifire\EventSubscriber\Sms\TwilioHandler;
use Fazland\NotifireBundle\Tests\Fixtures\AppKernel;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

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
}
