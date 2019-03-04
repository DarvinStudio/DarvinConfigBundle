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

use Darvin\Utils\DependencyInjection\ServiceSorter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Add configurations to pool compiler pass
 */
class AddConfigurationsPass implements CompilerPassInterface
{
    private const POOL_ID = 'darvin_config.configuration.pool';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $poolDefinition = $container->getDefinition(self::POOL_ID);
        $poolReference  = new Reference(self::POOL_ID);

        foreach (array_keys((new ServiceSorter())->sort($container->findTaggedServiceIds('darvin_config.configuration'))) as $id) {
            $container->getDefinition($id)->addMethodCall('setConfigurationPool', [$poolReference]);

            $poolDefinition->addMethodCall('addConfiguration', [new Reference($id)]);
        }
    }
}
