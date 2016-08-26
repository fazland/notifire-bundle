<?php

namespace Fazland\NotifireBundle\Tests\DependencyInjection\CompilerPass;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\VariableRendererPass;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class VariableRendererPassTest extends \PHPUnit_Framework_TestCase
{
    private $compilerPass;

    public function setUp()
    {
        $this->compilerPass = new VariableRendererPass();
    }

    public function testProcessShouldRegisterVariableRenderers()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $definition = $this->prophesize(Definition::class);

        $container->getDefinition('fazland.notifire.variable_renderer.factory')
            ->willReturn($definition->reveal())
        ;

        $container->findTaggedServiceIds('fazland.notifire.variable_renderer')
            ->willReturn([
                'renderer.one' => [],
                'renderer.two' => []
            ])
        ;

        $container->hasParameter('fazland.notifire.default_variable_renderer')->willReturn(false);

        $definition->addMethodCall('addRenderer', Argument::that(function ($arg) {
            return is_array($arg) && $arg[0] instanceof Reference && (string)$arg[0] === 'renderer.one';
        }))->shouldBeCalled();
        $definition->addMethodCall('addRenderer', Argument::that(function ($arg) {
            return is_array($arg) && $arg[0] instanceof Reference && (string)$arg[0] === 'renderer.two';
        }))->shouldBeCalled();


        $container->getDefinition('fazland.notifire.variable_renderer.factory')
            ->willReturn($definition->reveal())
        ;

        $this->compilerPass->process($container->reveal());
    }
}
