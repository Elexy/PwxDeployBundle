<?php

namespace Pwx\DeployBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements \Symfony\Component\Config\Definition\ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pwx_deploy');

        $rootNode
            ->children()
                ->booleanNode('enabled')->end()
                ->arrayNode('addons')
                  ->prototype('array')
                  ->children()
                  ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
