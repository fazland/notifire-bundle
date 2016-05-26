<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Alessandro Chitolina <alessandro.chitolina@fazland.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fazland_notifire');

        $this->addSwiftMailerSection($rootNode);
        $this->addMailgunSection($rootNode);
        $this->addTwilioSection($rootNode);

        return $treeBuilder;
    }

    private function addSwiftMailerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('swiftmailer')
                ->canBeEnabled()
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('mailer')
                ->children()
                    ->booleanNode('auto_configure_mailers')->defaultTrue()->end()
                    ->arrayNode('mailers')
                        ->prototype('scalar')
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addTwilioSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('twilio')
                ->canBeEnabled()
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('service')
                ->children()
                    ->arrayNode('services')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                            ->scalarNode('name')
                                ->validate()->ifNull()->thenInvalid('Missing value of name attribute')->end()
                            ->end()
                            ->scalarNode('account_sid')
                                ->validate()->ifNull()->thenInvalid('Missing value of service attribute')->end()
                            ->end()
                            ->scalarNode('auth_token')
                                ->validate()->ifNull()->thenInvalid('Missing value of service attribute')->end()
                            ->end()
                            ->scalarNode('from_phone')
                                ->validate()->ifNull()->thenInvalid('Missing value of service attribute')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addMailgunSection($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('mailgun')
                ->canBeEnabled()
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('mailer')
                ->children()
                    ->arrayNode('mailers')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('api_key')
                                    ->validate()->ifNull()->thenInvalid('Api key attribute is missing')->end()
                                ->end()
                                ->scalarNode('domain')
                                    ->validate()->ifNull()->thenInvalid('Domain attribute is missing')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
