<?php

namespace Fazland\NotifireBundle;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\SwiftMailerConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotifireBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SwiftMailerConfigurationPass());
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->container
            ->get('fazland.notifire.builder')
            ->initialize()
        ;
    }
}
