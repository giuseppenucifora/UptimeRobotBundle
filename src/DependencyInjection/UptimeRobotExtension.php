<?php

declare(strict_types=1);

namespace Pn\UptimeRobotBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class UptimeRobotExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

	    $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
	    $loader->load('services.yaml');

	    //$container->setParameter('uptime_robot.credentials', $config['credentials']);
        if (isset($config['credentials'])) {
            if (\array_key_exists('api_key', $config['credentials'])) {
                $container->setParameter('uptime_robot.credentials.api_key', $config['credentials']['api_key']);
            }
        }
        //$container->setParameter('uptime_robot.configurations', $config['configurations']);
	    if (isset($config['configurations'])) {
		    if (\array_key_exists('interval', $config['configurations'])) {
			    $container->setParameter('uptime_robot.configurations.interval', $config['configurations']['interval']);
		    }

		    if (\array_key_exists('alert_contacts', $config['configurations'])) {
			    $container->setParameter('uptime_robot.configurations.alert_contacts', $config['configurations']['alert_contacts']);
		    }
	    }

	    $this->addAnnotatedClassesToCompile([
			'Pn\\UptimeRobotBundle\\Service\\UptimeRobotApiService'
	    ]);
    }
}
