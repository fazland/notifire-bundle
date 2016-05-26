<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Fazland\Notifire\Notification\Email;
use Fazland\Notifire\Notification\Sms;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $notifireBuilderDefinition = $container->getDefinition('fazland.notifire.builder');

        $notifireBuilderDefinition->addMethodCall('addNotification', ['email', Email::class]);
        $notifireBuilderDefinition->addMethodCall('addNotification', ['sms', Sms::class]);

        if (isset($config['swiftmailer'])) {
            $swiftMailerConfig = $config['swiftmailer'];
            $container->setParameter('fazland.notifire.handler.swiftmailer.enabled', $swiftMailerConfig['enabled']);
            $container->setParameter(
                'fazland.notifire.handler.swiftmailer.auto_configure_mailers',
                $swiftMailerConfig['auto_configure_mailers']
            );
        }

        if (isset($config['twilio'])) {
            $twilioConfig = $config['twilio'];
            $container->setParameter('fazland.notifire.handler.twilio.enabled', $twilioConfig['enabled']);
            $container->setParameter(
                'fazland.notifire.handler.twilio.auto_configure_services',
                $twilioConfig['auto_configure_services']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://fazland.com/schema/dic/' . $this->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }
}
