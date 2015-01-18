<?php

namespace stz184\CaptchaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('stz184_captcha');
        $rootNode->children()
            ->scalarNode('session_key')->defaultValue('captcha_code')->end()
            ->integerNode('width')->defaultValue(146)->end()
            ->integerNode('height')->defaultValue(30)->end()
            ->booleanNode('background_noise')->defaultValue(true)->end()
            ->arrayNode('noise_color')
                ->treatNullLike(array())
                ->prototype('scalar')
                ->end()
                ->defaultValue(array('#FFFFFF', '#ffffb2'))
            ->end()
            ->arrayNode('background_color')
                ->treatNullLike(array())
                ->prototype('scalar')
                ->end()
                ->defaultValue(array('#beefff'))
            ->end()
            ->arrayNode('font_color')
                ->treatNullLike(array())
                ->prototype('scalar')
                ->end()
                ->defaultValue(array('#ff0000', '#33A6CF', '#00bf13'))
            ->end()
            ->scalarNode('font_path')->defaultValue('stz184CaptchaBundle/Resources/fonts/offshore.ttf')->end()
        ->end();
        return $treeBuilder;
    }
}
