<?php

namespace Fazland\NotifireBundle\DependencyInjection;

use Fazland\SkebbyRestClient\Constant\SendMethods;
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
                                        ->values(['swiftmailer', 'mailgun', 'composite'])
                                    ->end()
                                    ->arrayNode('composite')
                                        ->fixXmlConfig('provider')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('providers')
                                                ->prototype('scalar')->end()
                                            ->end()
                                            ->scalarNode('strategy')
                                                ->defaultValue('rand')
                                            ->end()
                                        ->end()
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
        $smsService = $rootNode
            ->children()
                ->arrayNode('sms')
                    ->fixXmlConfig('service')
                    ->canBeEnabled()
                    ->children()
        ;

        $service = $smsService
            ->arrayNode('services')
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->validate()
                        ->ifTrue(function ($value) {
                            return false;
                        })
                        ->thenInvalid('Invalid SMS service configuration')
                    ->end()
                    ->children()
        ;

        $service
            ->enumNode('provider')
                ->isRequired()
                ->values(['twilio', 'skebby', 'composite'])
            ->end()
            ->arrayNode('composite')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('providers')
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('strategy')
                        ->defaultValue('rand')
                    ->end()
                ->end()
            ->end()
            ->scalarNode('username')
                ->info('Skebby username / Twilio account SID')
                ->defaultNull()
            ->end()
            ->scalarNode('password')
                ->info('Skebby password / Twilio auth token')
                ->defaultNull()
            ->end()
            ->scalarNode('sender')
                ->info('SMS Sender')
                ->defaultNull()
            ->end()
            ->scalarNode('logger_service')
                ->info('Logger service')
                ->defaultNull()
            ->end()
        ;

        if (class_exists(SendMethods::class)) {
            $service
                ->enumNode('method')
                    ->info('Skebby send method')
                    ->values(SendMethods::all())
                    ->defaultValue(SendMethods::BASIC)
                ->end()
            ;
        }
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
