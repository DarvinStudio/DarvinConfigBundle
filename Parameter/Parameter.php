<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 14.08.15
 * Time: 9:14
 */

namespace Darvin\ConfigBundle\Parameter;

/**
 * Parameter
 */
class Parameter
{
    /**
     * @var string
     */
    private $configurationName;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $configurationName Configuration name
     * @param string $name              Name
     * @param string $type              Type
     * @param mixed  $value             Value
     */
    public function __construct($configurationName, $name, $type, $value)
    {
        $this->configurationName = $configurationName;
        $this->name = $name;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getConfigurationName()
    {
        return $this->configurationName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
