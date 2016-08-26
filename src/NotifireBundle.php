<?php

namespace Fazland\NotifireBundle;

use Fazland\NotifireBundle\DependencyInjection\CompilerPass\ExtensionPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\MailgunConfigurationPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\RegisterHandlerPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\SwiftMailerConfigurationPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\TwilioConfigurationPass;
use Fazland\NotifireBundle\DependencyInjection\CompilerPass\VariableRendererPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class NotifireBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new ExtensionPass())
            ->addCompilerPass(new SwiftMailerConfigurationPass())
            ->addCompilerPass(new TwilioConfigurationPass())
            ->addCompilerPass(new MailgunConfigurationPass())
            ->addCompilerPass(new VariableRendererPass())
            ->addCompilerPass(new RegisterHandlerPass(), PassConfig::TYPE_BEFORE_REMOVING)
        ;
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
