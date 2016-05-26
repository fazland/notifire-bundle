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
            $twilioAccountSid = $parameters['account_sid'];
            $twilioAuthToken = $parameters['auth_token'];

            $twilioHandlerDefinition = new DefinitionDecorator('fazland.notifire.handler.twilio.prototype');
            $twilioHandlerDefinition
                ->setPublic(true)
                ->setAbstract(false)
                ->replaceArgument(0, new Reference($this->createTwilioServiceDefinition(
                    $container,
                    $name,
                    $twilioAccountSid,
                    $twilioAuthToken
                )))
                ->replaceArgument(1, $name)
                ->addTag('kernel.event_subscriber')
            ;

            $container->setDefinition("fazland.notifire.handler.twilio.$name", $twilioHandlerDefinition);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string $name
     * @param string $twilioAccountSid
     * @param string $twilioAuthToken
     *
     * @return string
     */
    protected function createTwilioServiceDefinition(
        ContainerBuilder $container,
        $name,
        $twilioAccountSid,
        $twilioAuthToken
    ) {
        $definition = new Definition(\Services_Twilio::class, [$twilioAccountSid, $twilioAuthToken]);
        $definition->addTag("fazland.notifire.twilio.$name");

        $definitionId = "fazland.notifire.twilio.service.$name";
        $container->setDefinition($definitionId, $definition);

        return $definitionId;
    }
}
