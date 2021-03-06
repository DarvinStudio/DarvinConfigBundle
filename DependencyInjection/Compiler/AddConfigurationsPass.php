<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\DependencyInjection\Compiler;

use Darvin\ConfigBundle\DependencyInjection\DarvinConfigExtension;
use Darvin\Utils\DependencyInjection\ServiceSorter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add configurations to pool compiler pass
 */
class AddConfigurationsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $parameterRepository     = [new Reference('darvin_config.parameter.repository')];
        $parameterValueConverter = [new Reference('darvin_config.parameter.value_converter')];
        $pool                    = $container->getDefinition('darvin_config.configuration.pool');

        foreach (array_keys((new ServiceSorter())->sort($container->findTaggedServiceIds(DarvinConfigExtension::TAG_CONFIGURATION))) as $id) {
            $container->getDefinition($id)
                ->addMethodCall('setParameterRepository', $parameterRepository)
                ->addMethodCall('setParameterValueConverter', $parameterValueConverter);

            $pool->addMethodCall('addConfiguration', [new Reference($id)]);
        }
    }
}
