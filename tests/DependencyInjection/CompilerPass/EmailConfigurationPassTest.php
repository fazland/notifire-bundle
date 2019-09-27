<?php declare(strict_types=1);

namespace Fazland\NotifierBundle\Tests\DependencyInjection\CompilerPass;

use Fazland\Notifire\Handler\Email\SwiftMailerHandler;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\EmailConfigurationPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class EmailConfigurationPassTest extends TestCase
{
    /**
     * @var EmailConfigurationPass
     */
    private $pass;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->pass = new EmailConfigurationPass();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('fazland.notifire.emails.enabled', true);
    }

    public function testShouldNotAddServiceIfDisabled(): void
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container->getParameter('fazland.notifire.emails.enabled')->willReturn(false);

        $container->register(Argument::cetera())->shouldNotBeCalled();
        $container->setDefinition(Argument::cetera())->shouldNotBeCalled();

        $this->pass->process($container->reveal());
    }

    public function testShouldAutoRegisterSwiftmailerMailers(): void
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
                    'transport' => 'smtp',
                ],
                'second' => [
                    'transport' => 'custom_transport',
                ],
            ],
        ]);

        $this->container->register('fazland.notifire.handler.swiftmailer.prototype', SwiftMailerHandler::class)
            ->setAbstract(true)
            ->setPublic(false)
            ->setArguments([null, null])
        ;

        $this->pass->process($this->container);

        self::assertTrue($this->container->has('fazland.notifire.handler.email.first'));
        self::assertTrue($this->container->has('fazland.notifire.handler.email.second'));

        self::assertEquals(SwiftMailerHandler::class, $this->container->getDefinition('fazland.notifire.handler.email.first')->getClass());
        self::assertEquals(SwiftMailerHandler::class, $this->container->getDefinition('fazland.notifire.handler.email.second')->getClass());
    }
}
