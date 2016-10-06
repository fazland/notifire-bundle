<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class NotifireExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->processEmails($container, $config['email']);
        $this->processSms($container, $config['sms']);

        $this->processDefaultVariableRenderer($container, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://fazland.com/schema/dic/'.$this->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    private function processEmails(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.emails.enabled', $config['enabled']);
        if (!$config['enabled']) {
            return;
        }

        $container->setParameter('fazland.notifire.emails.autoconfigure_swiftmailer', $config['auto_configure_swiftmailer']);
        $container->setParameter('fazland.notifire.emails.mailers', $config['mailers']);
    }

    private function processSms(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.sms.enabled', $config['enabled']);
        if (!$config['enabled']) {
            return;
        }

        foreach ($config['services'] as $name => $service) {
            if ($service['provider'] === 'twilio') {
                $account_sid = $service['account_sid'];
                $auth_token = $service['auth_token'];

                $serviceId = $this->createTwilioService($container, $name, $account_sid, $auth_token);

                $definition = clone $container->getDefinition('fazland.notifire.handler.twilio.prototype');
                $definition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference($serviceId))
                    ->replaceArgument(1, $name)
                    ->addMethodCall('setDefaultFrom', [$service['from_phone']])
                ;

                $container->setDefinition("fazland.notifire.handler.twilio.$name", $definition);
            } else {
                throw new InvalidConfigurationException('Unknown provider "'.$service['provider'].'"');
            }
        }
    }

    private function createTwilioService(ContainerBuilder $container, $name, $sid, $token)
    {
        $definition = new Definition(\Services_Twilio::class, [$sid, $token]);
        $definition->addTag("fazland.notifire.twilio.$name");

        $definitionId = "fazland.notifire.twilio.service.$name";
        $container->setDefinition($definitionId, $definition);

        return $definitionId;
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function processDefaultVariableRenderer(ContainerBuilder $container, array $config)
    {
        if (isset($config['default_variable_renderer'])) {
            $container->setParameter('fazland.notifire.default_variable_renderer', $config['default_variable_renderer']);
        }
    }
}
