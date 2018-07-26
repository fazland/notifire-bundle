<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TwigProcessorRemoverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('twig')) {
            $container->removeDefinition('fazland.notifire.processor.twig_processor');
        }
    }
}
