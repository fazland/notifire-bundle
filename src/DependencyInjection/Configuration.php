<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $this->addTwilioSection($rootNode);

        return $treeBuilder;
    }

    private function addSwiftMailerSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('swiftmailer')
                ->canBeEnabled()
                ->treatFalseLike(['enabled' => false])
                ->treatTrueLike(['enabled' => true])
                ->treatNullLike(['enabled' => true])
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultNull()->end()
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
                ->treatFalseLike(['enabled' => false])
                ->treatTrueLike(['enabled' => true])
                ->treatNullLike(['enabled' => true])
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultNull()->end()
                    ->arrayNode('services')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->children()
                            ->scalarNode('name')
                                ->validate()->ifNull()->thenInvalid()->end()
                            ->end()
                            ->scalarNode('service')
                                ->validate()->ifNull()->thenInvalid()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}