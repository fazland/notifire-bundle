<?php

namespace Fazland\NotifierBundle\Tests\DependencyInjection\CompilerPass;

use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\EmailConfigurationPass;
use Prophecy\Argument;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\SwiftmailerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class EmailConfigurationPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailConfigurationPass
     */
    private $pass;

    /**
     * @var ContainerBuilder
     */
    private $container;

    public function setUp()
    {
        $this->pass = new EmailConfigurationPass();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('fazland.notifire.emails.enabled', true);
    }

    public function testShouldNotAddServiceIfDisabled()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('fazland.notifire.emails.enabled')->willReturn(false);

        $container->register(Argument::cetera())->shouldNotBeCalled();
        $container->setDefinition(Argument::cetera())->shouldNotBeCalled();

        $this->pass->process($container->reveal());
    }

    public function testShouldAutoregisterSwiftmailerMailers()
    {
        $swiftExtension = $this->prophesize(ExtensionInterface::class);
        $swiftExtension->getAlias()->willReturn('swiftmailer');
        $swiftExtension->getNamespace()->willReturn(__NAMESPACE__);

        $this->container->setParameter('fazland.notifire.emails.autoconfigure_swiftmailer', true);
        $this->container->setParameter('kernel.debug', true);
        $this->container->registerExtension($swiftExtension->reveal());
        $this->container->loadFromExtension('swiftmailer', [
            'mailers' => [
                'first' => [
                    'transport' => 'smtp'
                ],
                'second' => [
                    'transport' => 'custom_transport'
                ]
            ]
        ]);

        $this->container->register('fazland.notifire.handler.swiftmailer.prototype', SwiftMailerHandler::class)
            ->setAbstract(true)
            ->setPublic(false)
            ->setArguments([null, null]);

        $this->pass->process($this->container);

        $this->assertTrue($this->container->has('fazland.notifire.handler.email.first'));
        $this->assertTrue($this->container->has('fazland.notifire.handler.email.second'));

        $this->assertEquals(SwiftMailerHandler::class, $this->container->getDefinition('fazland.notifire.handler.email.first')->getClass());
        $this->assertEquals(SwiftMailerHandler::class, $this->container->getDefinition('fazland.notifire.handler.email.second')->getClass());
    }
}
