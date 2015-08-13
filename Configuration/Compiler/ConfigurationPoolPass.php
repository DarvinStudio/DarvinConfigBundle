<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 16:00
 */

namespace Darvin\ConfigBundle\Configuration\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Configuration pool compiler pass
 */
class ConfigurationPoolPass implements CompilerPassInterface
{
    const TAG_CONFIGURATION = 'darvin_config.configuration';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $pool = $container->getDefinition('darvin_config.configuration.pool');

        foreach ($container->findTaggedServiceIds(self::TAG_CONFIGURATION) as $id => $attr) {
            $pool->addMethodCall('add', array(
                new Reference($id),
            ));
        }
    }
}
