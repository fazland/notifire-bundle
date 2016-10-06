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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fazland_notifire');

        $this->addEmailSection($rootNode);
        $this->addSmsSection($rootNode);

        $this->addDefaultRendererSection($rootNode);

        return $treeBuilder;
    }

    private function addEmailSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('email')
                    ->fixXmlConfig('mailer')
                    ->canBeEnabled()
                    ->children()
                        ->booleanNode('auto_configure_swiftmailer')->defaultTrue()->end()
                        ->arrayNode('mailers')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->enumNode('provider')
                                        ->isRequired()
                                        ->values(['swiftmailer', 'mailgun'])
                                    ->end()
                                    ->scalarNode('mailer_name')->defaultNull()->end()
                                    ->scalarNode('api_key')->defaultNull()->end()
                                    ->scalarNode('domain')->defaultNull()->end()
                                ->end()
                                ->validate()
                                    ->ifTrue(function ($value) {
                                        if ($value['provider'] === 'mailgun' && (!isset($value['api_key']) || !isset($value['domain']))) {
                                            return true;
                                        }

                                        return false;
                                    })
                                    ->thenInvalid('Invalid mailer configuration')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function addSmsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('sms')
                    ->fixXmlConfig('service')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('services')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('provider')
                                        ->isRequired()
                                        ->validate()->ifNotInArray(['twilio'])->thenInvalid('Invalid SMS provider %s')->end()
                                    ->end()
                                    ->scalarNode('account_sid')->defaultNull()->end()
                                    ->scalarNode('auth_token')->defaultNull()->end()
                                    ->scalarNode('from_phone')->defaultNull()->end()
                                ->end()
                                ->validate()
                                    ->ifTrue(function ($value) {
                                        return false;
                                    })
                                    ->thenInvalid('Invalid SMS service configuration')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addDefaultRendererSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('default_variable_renderer')
                ->end()
            ->end()
        ;
    }
}
