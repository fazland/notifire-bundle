<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fazland.notifire.builder');

        foreach ($container->findTaggedServiceIds('fazland.notifire.extension') as $serviceId => $unused) {
            $definition->addMethodCall('addExtension', [new Reference($serviceId)]);
        }
    }
}
