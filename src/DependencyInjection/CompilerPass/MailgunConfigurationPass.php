<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Mailgun\Mailgun;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class MailgunConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->getParameter('fazland.notifire.handler.mailgun.enabled')) {
            return;
        }

        $mailers = $container->getParameter('fazland.notifire.handler.mailgun.mailers');
        foreach ($mailers as $parameters) {
            $domain = $parameters['domain'];

            $id = $this->createMailgunService($container, $parameters);

            $definition = clone $container->getDefinition('fazland.notifire.handler.mailgun.prototype');
            $definition
                ->setPublic(true)
                ->setAbstract(false)
                ->replaceArgument(0, new Reference($id))
                ->replaceArgument(1, $domain)
            ;

            $container->setDefinition("fazland.notifire.handler.mailgun.$domain", $definition);
        }
    }

    private function createMailgunService(ContainerBuilder $container, array $parameters)
    {
        $apiKey = $parameters['api_key'];
        $domain = $parameters['domain'];

        $definition = new Definition(Mailgun::class, [$apiKey]);
        $container->setDefinition(($id = 'fazland.notifire.mailgun.'.$domain), $definition);

        return $id;
    }
}