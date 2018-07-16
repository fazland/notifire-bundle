<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\DependencyInjection;

use Fazland\SkebbyRestClient\Client\Client as SkebbyRestClient;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Twilio\Rest\Client;

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

        if (! $container->hasDefinition('twig')) {
            $container->removeDefinition('fazland.notifire.processor.twig_processor');
        }

        $this->processEmails($container, $config['email']);
        $this->processSms($container, $config['sms']);

        $this->processDefaultVariableRenderer($container, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace(): string
    {
        return 'http://fazland.com/schema/dic/'.$this->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath(): string
    {
        return __DIR__.'/../Resources/config/schema';
    }

    private function processEmails(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.emails.enabled', $config['enabled']);
        if (! $config['enabled']) {
            return;
        }

        $container->setParameter('fazland.notifire.emails.autoconfigure_swiftmailer', $config['auto_configure_swiftmailer']);
        $container->setParameter('fazland.notifire.emails.mailers', $config['mailers']);
    }

    private function processSms(ContainerBuilder $container, array $config)
    {
        $container->setParameter('fazland.notifire.sms.enabled', $config['enabled']);
        if (! $config['enabled']) {
            return;
        }

        foreach ($config['services'] as $name => $service) {
            $serviceName = 'fazland.notifire.handler.sms.'.$name;

            if ('twilio' === $service['provider']) {
                $account_sid = $service['username'];
                $auth_token = $service['password'];

                $serviceId = $this->createTwilioService($container, $name, $account_sid, $auth_token);

                $definition = clone $container->getDefinition('fazland.notifire.handler.twilio.prototype');
                $definition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference($serviceId))
                    ->replaceArgument(1, $name)
                ;

                if (isset($service['sender'])) {
                    $definition->addMethodCall('setDefaultFrom', [$service['sender']]);
                }

                if (isset($service['twilio_messaging_service_sid'])) {
                    $definition->addMethodCall('setMessagingServiceSid', [$service['twilio_messaging_service_sid']]);
                }

                $container->setDefinition($serviceName, $definition);
            } elseif ('skebby' === $service['provider']) {
                $serviceId = $this->createSkebbyClient($container, $name, $service['username'], $service['password'], $service['sender'], $service['method']);

                $definition = clone $container->getDefinition('fazland.notifire.handler.skebby.prototype');
                $definition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference($serviceId))
                    ->replaceArgument(1, $name)
                ;

                $container->setDefinition($serviceName, $definition);
            } elseif ('composite' === $service['provider']) {
                $config = $service['composite'];

                if (empty($config['providers'])) {
                    throw new InvalidConfigurationException('Empty provider list for sms service '.$name);
                }

                $strategy = $config['strategy'];
                if (in_array($strategy, ['rand'])) {
                    $strategy = 'fazland.notifire.handler_choice_strategy.'.$strategy;
                }

                $handler = $container->register($serviceName, $container->getParameter('fazland.notifire.handler.composite.prototype.class'))
                    ->addArgument($name)
                    ->addArgument(new Reference($strategy))
                    ->addTag('fazland.notifire.handler');

                foreach ($config['providers'] as $provider_name) {
                    $handler->addMethodCall('addNotificationHandler', [new Reference('fazland.notifire.handler.sms.'.$provider_name)]);
                }
            } else {
                throw new InvalidConfigurationException('Unknown provider "'.$service['provider'].'"');
            }

            if (isset($service['logger_service'])) {
                $definition = $container->getDefinition($serviceName);
                $definition->addMethodCall('setLogger', [new Reference($service['logger_service'])]);
            }
        }
    }

    protected function createTwilioService(
        ContainerBuilder $container,
        string $name,
        string $sid,
        string $token
    ): string {
        $definition = new Definition(
            ! class_exists(Client::class) ? \Services_Twilio::class : Client::class,
            [$sid, $token]
        );
        $definition->addTag("fazland.notifire.twilio.$name");

        $definitionId = "fazland.notifire.twilio.service.$name";
        $container->setDefinition($definitionId, $definition);

        return $definitionId;
    }

    protected function createSkebbyClient(
        ContainerBuilder $container,
        string $name,
        string $username,
        string $password,
        string $sender,
        string $method
    ): string {
        $definitionId = 'fazland.notifire.skebby.client.'.$name;
        $container->register($definitionId, SkebbyRestClient::class)
            ->addArgument([
                'username' => $username,
                'password' => $password,
                'sender' => $sender,
                'method' => $method,
            ]);

        return $definitionId;
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function processDefaultVariableRenderer(ContainerBuilder $container, array $config)
    {
        if (isset($config['default_variable_renderer'])) {
            $container->setParameter('fazland.notifire.default_variable_renderer', $config['default_variable_renderer']);
        }
    }
}
