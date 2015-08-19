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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configuration pool compiler pass
 */
class ConfigurationPoolPass implements CompilerPassInterface
{
    const POOL_ID = 'darvin_config.configuration.pool';

    const TAG_CONFIGURATION = 'darvin_config.configuration';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $configurationIds = $container->findTaggedServiceIds(self::TAG_CONFIGURATION);

        if (empty($configurationIds)) {
            return;
        }

        $pool = $container->getDefinition(self::POOL_ID);
        $poolReference = new Reference(self::POOL_ID);

        foreach ($configurationIds as $id => $attr) {
            $configuration = $container->getDefinition($id);
            $configuration->addMethodCall('setConfigurationPool', array(
                $poolReference,
            ));

            $pool->addMethodCall('add', array(
                new Reference($id),
            ));
        }
    }
}
