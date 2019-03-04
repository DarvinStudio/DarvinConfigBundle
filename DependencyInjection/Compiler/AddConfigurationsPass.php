<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\DependencyInjection\Compiler;

use Darvin\Utils\DependencyInjection\ServiceSorter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add configurations compiler pass
 */
class AddConfigurationsPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_config.configuration.pool';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds('darvin_config.configuration');

        $ids = (new ServiceSorter())->sort($ids);

        $this->addConfigurations($container, array_keys($ids));
    }

    /**
     * @deprecated
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container DI container
     * @param string[]                                                $ids       Service IDs
     */
    public function addConfigurations(ContainerBuilder $container, array $ids)
    {
        if (empty($ids) || !$container->hasDefinition(self::POOL_ID)) {
            return;
        }

        $poolDefinition = $container->getDefinition(self::POOL_ID);
        $poolReference = new Reference(self::POOL_ID);

        foreach ($ids as $id) {
            $configurationDefinition = $container->getDefinition($id);
            $configurationDefinition->addMethodCall('setConfigurationPool', [
                $poolReference,
            ]);

            $poolDefinition->addMethodCall('addConfiguration', [
                new Reference($id),
            ]);
        }
    }
}
