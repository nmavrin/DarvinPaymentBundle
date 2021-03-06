<?php

namespace Darvin\PaymentBundle\DependencyInjection;

use Darvin\PaymentBundle\PaymentManager\DefaultPaymentManager;
use Darvin\PaymentBundle\UrlBuilder\DefaultPaymentUrlBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DarvinPaymentExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->updatePaymentManagerService($container, $config);
        $this->updateUrlBuilderService($container, $config);

        foreach ($config['parameters_bridge'] as $key => $gatewayConfig) {
            $container->setParameter('darvin_payment.config.gateway_parameters_bridge.'.$key, $gatewayConfig);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function updatePaymentManagerService(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition(DefaultPaymentManager::class);
        $definition->setArgument(2, $config['payment_class']);
        $definition->setArgument(3, $config['default_currency']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    protected function updateUrlBuilderService(ContainerBuilder $container, array $config)
    {
        $definition = $container->getDefinition(DefaultPaymentUrlBuilder::class);
        $definition->setArgument(1, $config['default_gateway']);
    }

    public function prepend(ContainerBuilder $container)
    {
        $fileLocator = new FileLocator(__DIR__.'/../Resources/config/app');

        foreach ([
                     'doctrine',
                 ] as $extension) {
            if ($container->hasExtension($extension)) {
                $container->prependExtensionConfig($extension, Yaml::parse(file_get_contents($fileLocator->locate($extension.'.yml')))[$extension]);
            }
        }
    }
}
