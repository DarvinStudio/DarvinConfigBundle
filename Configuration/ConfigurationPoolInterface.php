<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Configuration;

/**
 * Configuration pool
 */
interface ConfigurationPoolInterface
{
    /**
     * @param string $configurationName Configuration name
     *
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface
     * @throws \InvalidArgumentException
     */
    public function __get(string $configurationName): ConfigurationInterface;

    /**
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    public function getAllConfigurations(): array;

    /**
     * Saves configurations.
     */
    public function saveAll(): void;
}
