<?php

namespace Fazland\NotifireBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class TwilioConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->getParameter('fazland.notifire.handler.twilio.enabled')) {
            return;
        }
        
        $twilioServices = $container->getParameter('fazland.notifire.handler.twilio.services');
        foreach ($twilioServices as $name => $parameters) {
            $account_sid = $parameters['account_sid'];
            $auth_token = $parameters['auth_token'];

            $service = $this->createTwilioServiceDefinition($container, $name, $account_sid, $auth_token);

            $definition = clone $container->getDefinition('fazland.notifire.handler.twilio.prototype');
            $definition
                ->setPublic(true)
                ->setAbstract(false)
                ->replaceArgument(0, new Reference($service))
                ->replaceArgument(1, $name)
            ;

            $container->setDefinition("fazland.notifire.handler.twilio.$name", $definition);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $name
     * @param string $sid
     * @param string $token
     *
     * @return string
     */
    protected function createTwilioServiceDefinition(ContainerBuilder $container, $name, $sid, $token)
    {
        $definition = new Definition(\Services_Twilio::class, [$sid, $token]);
        $definition->addTag("fazland.notifire.twilio.$name");

        $definitionId = "fazland.notifire.twilio.service.$name";
        $container->setDefinition($definitionId, $definition);

        return $definitionId;
    }
}
