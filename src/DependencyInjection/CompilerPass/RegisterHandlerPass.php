<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterHandlerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fazland.notifire.builder');

        foreach ($container->findTaggedServiceIds('fazland.notifire.handler') as $serviceId => $unused) {
            $def = $container->getDefinition($serviceId);
            if ($def->isAbstract()) {
                continue;
            }

            $def->setLazy(true);
            $definition->addMethodCall('addHandler', [new Reference($serviceId)]);
        }
    }
}
