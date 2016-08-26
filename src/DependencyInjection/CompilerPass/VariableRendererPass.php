<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class VariableRendererPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fazland.notifire.variable_renderer.factory');

        foreach ($container->findTaggedServiceIds('fazland.notifire.variable_renderer') as $serviceId => $unused) {
            $definition->addMethodCall('addRenderer', [new Reference($serviceId)]);
        }

        if ($container->hasParameter('fazland.notifire.default_variable_renderer')) {
            $defaultRenderer = $container->getParameter('fazland.notifire.default_variable_renderer');
            $definition = $container->getDefinition('fazland.notifire.twig_extension.variable_renderer_extension');

            $definition->addMethodCall('setDefaultRenderer', [$defaultRenderer]);
        }
    }
}
