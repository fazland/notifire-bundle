<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\tests\DependencyInjection\CompilerPass;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\ExtensionPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPassTest extends TestCase
{
    /**
     * @var CompilerPassInterface
     */
    private $compilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compilerPass = new ExtensionPass();
    }

    public function testProcessShouldAddExtensionsCallToTheBuilder()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $definition = $this->prophesize(Definition::class);

        $container->getDefinition('fazland.notifire.builder')
            ->willReturn($definition->reveal())
        ;

        $container->findTaggedServiceIds('fazland.notifire.extension')
            ->willReturn([
                'extension.one' => [],
                'extension.two' => [],
            ])
        ;

        $definition->addMethodCall('addExtension', Argument::that(function ($arg) {
            return \is_array($arg) && $arg[0] instanceof Reference && 'extension.one' === (string) $arg[0];
        }))->shouldBeCalled();
        $definition->addMethodCall('addExtension', Argument::that(function ($arg) {
            return \is_array($arg) && $arg[0] instanceof Reference && 'extension.two' === (string) $arg[0];
        }))->shouldBeCalled();

        $this->compilerPass->process($container->reveal());
    }
}
