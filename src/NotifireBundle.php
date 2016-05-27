<?php

namespace Fazland\NotifireBundle;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\ExtensionPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\MailgunConfigurationPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\SwiftMailerConfigurationPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\TwilioConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotifireBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExtensionPass());
        $container->addCompilerPass(new SwiftMailerConfigurationPass());
        $container->addCompilerPass(new TwilioConfigurationPass());
        $container->addCompilerPass(new MailgunConfigurationPass());
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
