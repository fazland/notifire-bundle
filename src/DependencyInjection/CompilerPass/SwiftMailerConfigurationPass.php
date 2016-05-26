<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\Configuration as SwiftMailerConfiguration;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class SwiftMailerConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasExtension('swiftmailer') ||
            false === $container->getParameter('fazland.notifire.handler.swiftmailer.enabled')) {
            return;
        }

        $swiftMailerConfigs = $container->getExtensionConfig('swiftmailer');
        $swiftMailerConfiguration = new SwiftMailerConfiguration($container->getParameter('kernel.debug'));

        $processor = new Processor();

        $swiftMailerConfig = $processor->processConfiguration($swiftMailerConfiguration, $swiftMailerConfigs);
        $swiftMailerConfig = $container->getParameterBag()->resolveValue($swiftMailerConfig);

        if ($container->getParameter('fazland.notifire.handler.swiftmailer.auto_configure_mailers')) {
            foreach ($swiftMailerConfig['mailers'] as $name => $config) {
                $swiftMailerHandlerDefinition = new DefinitionDecorator(
                    'fazland.notifire.handler.swiftmailer.prototype'
                );

                $swiftMailerHandlerDefinition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference("swiftmailer.mailer.$name"))
                    ->replaceArgument(1, $name)
                    ->addTag('kernel.event_subscriber')
                ;

                $definitionName = "fazland.notifire.handler.swiftmailer.$name";
                $container->setDefinition($definitionName, $swiftMailerHandlerDefinition);
            }
        }
    }
}
