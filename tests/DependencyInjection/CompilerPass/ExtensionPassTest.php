<?php

namespace Fazland\NotifireBundle\Tests\DependencyInjection\CompilerPass;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\ExtensionPass;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPassTest extends \PHPUnit_Framework_TestCase
{
    private $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new ExtensionPass();
    }

    public function testProcessShouldAddExtensionsCallToTheBuilder()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $definition = $this->prophesize(Definition::class);

        $container->getDefinition('fazland.notifire.builder')
            ->willReturn($definition->reveal());

        $container->findTaggedServiceIds('fazland.notifire.extension')
            ->willReturn([
                'extension.one' => [],
                'extension.two' => []
            ]);

        $definition->addMethodCall('addExtension', Argument::that(function ($arg) {
            return is_array($arg) && $arg[0] instanceof Reference && (string)$arg[0] === 'extension.one';
        }))->shouldBeCalled();
        $definition->addMethodCall('addExtension', Argument::that(function ($arg) {
            return is_array($arg) && $arg[0] instanceof Reference && (string)$arg[0] === 'extension.two';
        }))->shouldBeCalled();

        $this->compilerPass->process($container->reveal());
    }
}
