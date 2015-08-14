<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 15:57
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
