<?php

declare(strict_types=1);

namespace Pn\UptimeRobotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('uptime_robot');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('credentials')
                    ->children()
                        ->variableNode('api_key')->end()
                    ->end()
                ->end()
	            ->arrayNode('configurations')
	                ->children()
	                    ->variableNode('interval')->end()
	                    ->variableNode('alert_contacts')->end()
	                ->end()
	            ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
