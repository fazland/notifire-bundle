<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\DependencyInjection\CompilerPass;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\VariableRendererPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class VariableRendererPassTest extends TestCase
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
                'renderer.two' => [],
            ])
        ;

        $container->hasParameter('fazland.notifire.default_variable_renderer')->willReturn(false);

        $definition->addMethodCall('addRenderer', Argument::that(function ($arg) {
            return \is_array($arg) && $arg[0] instanceof Reference && 'renderer.one' === (string) $arg[0];
        }))->shouldBeCalled();
        $definition->addMethodCall('addRenderer', Argument::that(function ($arg) {
            return \is_array($arg) && $arg[0] instanceof Reference && 'renderer.two' === (string) $arg[0];
        }))->shouldBeCalled();

        $container->getDefinition('fazland.notifire.variable_renderer.factory')
            ->willReturn($definition->reveal())
        ;

        $this->compilerPass->process($container->reveal());
    }
}
