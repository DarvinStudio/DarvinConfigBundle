<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Configuration;

/**
 * Configuration
 */
interface ConfigurationInterface
{
    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function setConfigurationPool(ConfigurationPool $configurationPool);

    /**
     * @return \Darvin\ConfigBundle\Parameter\ParameterModel[]
     */
    public function getModel();

    /**
     * Saves configuration.
     */
    public function save();

    /**
     * @param array $values Parameter values
     */
    public function setValues(array $values);

    /**
     * @return array
     */
    public function getValues();

    /**
     * @return string
     */
    public function getName();
}
