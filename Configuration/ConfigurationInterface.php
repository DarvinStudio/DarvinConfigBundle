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
     * @return \Darvin\ConfigBundle\Parameter\Parameter[]
     */
    public function getModel();

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
