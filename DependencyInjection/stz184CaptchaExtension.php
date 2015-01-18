<?php

namespace stz184\CaptchaBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class stz184CaptchaExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias() . '.config', $config);
        $container->setParameter($this->getAlias() . '.session_key', $config['session_key']);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');


        $container->setParameter(
            'twig.form.resources',
            array_merge(array('stz184CaptchaBundle::captcha.html.twig'), $container->getParameter('twig.form.resources'))
        );
    }

    public function getAlias()
    {
        return 'stz184_captcha';
    }
}
