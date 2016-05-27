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

        $this->processSwiftMailer($container, $config['swiftmailer']);
        $this->processMailgun($container, $config['mailgun']);
        $this->processTwilio($container, $config['twilio']);
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

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    protected function processSwiftMailer(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.handler.swiftmailer.enabled', $config['enabled']);
        $container->setParameter(
            'fazland.notifire.handler.swiftmailer.auto_configure_mailers',
            $config['auto_configure_mailers']
        );

        $container->setParameter('fazland.notifire.handler.swiftmailer.mailers', $config['mailers']);
    }

    /**
     * @param ContainerBuilder $container
     * @param $config
     */
    protected function processTwilio(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.handler.twilio.enabled', $config['enabled']);
        $container->setParameter('fazland.notifire.handler.twilio.services', $config['services']);
    }

    private function processMailgun(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.handler.mailgun.enabled', $config['enabled']);
        $container->setParameter('fazland.notifire.handler.mailgun.mailers', $config['mailers']);
    }
}
