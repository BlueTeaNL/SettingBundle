<?php

namespace Bluetea\SettingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BlueteaSettingExtension extends Extension
{
    private $config;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;

        $configuration = new Configuration();
        $this->config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->handleConfig();
    }

    /**
     * Handle configuration
     */
    protected function handleConfig()
    {
        $app = $this->config['app'];
        $entity = $this->config['entity'];

        $this->container->setParameter('bluetea_setting.entity.app_setting.class', $app['class']);
        $this->container->setParameter('bluetea_setting.entity.entity_setting.class', $entity['class']);
    }
}
