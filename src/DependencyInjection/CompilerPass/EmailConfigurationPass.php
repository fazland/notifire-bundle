<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Mailgun\Mailgun;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\Configuration as SwiftMailerConfiguration;

class EmailConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('fazland.notifire.emails.enabled')) {
            return;
        }

        $swift_mailers = [];
        if ($container->hasExtension('swiftmailer') && $container->getParameter('fazland.notifire.emails.autoconfigure_swiftmailer')) {
            $swift_mailers = $this->getSwiftMailers($container);
        }

        $mailers = array_merge($swift_mailers, $container->getParameter('fazland.notifire.emails.mailers'));
        foreach ($mailers as $name => $mailer) {
            if ($mailer['provider'] === 'swiftmailer') {
                $definition = clone $container->getDefinition('fazland.notifire.handler.swiftmailer.prototype');
                $mailer_name = $mailer['mailer_name'] ?: $name;

                $definition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference("swiftmailer.mailer.$mailer_name"))
                    ->replaceArgument(1, $name)
                ;

                $container->setDefinition("fazland.notifire.handler.swiftmailer.$name", $definition);
            } elseif ($mailer['provider'] === 'mailgun') {
                $domain = $mailer['domain'];

                $id = $this->createMailgunService($container, $mailer);

                $definition = clone $container->getDefinition('fazland.notifire.handler.mailgun.prototype');
                $definition
                    ->setPublic(true)
                    ->setAbstract(false)
                    ->replaceArgument(0, new Reference($id))
                    ->replaceArgument(1, $domain)
                    ->replaceArgument(2, $name)
                ;

                $container->setDefinition("fazland.notifire.handler.mailgun.$domain", $definition);
            } else {
                throw new InvalidConfigurationException('Unknown provider "'.$mailer['provider'].'"');
            }
        }
    }

    protected function getSwiftMailers(ContainerBuilder $container)
    {
        $swiftMailerConfigs = $container->getExtensionConfig('swiftmailer');
        $swiftMailerConfiguration = new SwiftMailerConfiguration($container->getParameter('kernel.debug'));

        $processor = new Processor();

        $swiftMailerConfig = $container->getParameterBag()->resolveValue($swiftMailerConfigs);
        $swiftMailerConfig = $processor->processConfiguration($swiftMailerConfiguration, $swiftMailerConfig);

        $mailers = [];
        foreach (array_keys($swiftMailerConfig['mailers']) as $name) {
            $mailers[$name] = ['provider' => 'swiftmailer', 'mailer_name' => $name];
        }

        return $mailers;
    }

    private function createMailgunService(ContainerBuilder $container, array $parameters)
    {
        $apiKey = $parameters['api_key'];
        $domain = $parameters['domain'];

        $container->register(($id = 'fazland.notifire.mailgun.'.$domain), Mailgun::class)
            ->setArguments([$apiKey]);

        return $id;
    }
}
