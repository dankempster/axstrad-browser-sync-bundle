<?php

namespace Axstrad\BrowserSyncBundle\DependencyInjection;

/**
 * Dependancies
 */
use Axstrad\BrowserSyncBundle\EventListener\BrowserSyncListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AxstradBrowserSyncExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (empty($config['server_ip']) && isset($_SERVER['SERVER_ADDR'])) {
            $config['server_ip'] = $_SERVER['SERVER_ADDR'];
        }
        if (isset($config['server_ip'])) {
            $container->setParameter('axstrad.browser_sync.server_ip', $config['server_ip']);
        }

        $container->setParameter('axstrad.browser_sync.mode', $config['mode'] === true
            ? BrowserSyncListener::ENABLED
            : BrowserSyncListener::DISABLED
        );
    }
}
