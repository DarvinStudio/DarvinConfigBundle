<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 14.08.15
 * Time: 8:59
 */

namespace Darvin\ConfigBundle\Configuration;

use Darvin\Utils\Strings\StringsUtil;

/**
 * Configuration abstract implementation
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var array
     */
    private $values;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = array();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigurationPool(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
    }

    /**
     * @param string $method    Method name
     * @param array  $arguments Arguments
     *
     * @return mixed
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function __call($method, array $arguments = array())
    {
        $this->configurationPool->init();

        $methodUnderscore = StringsUtil::toUnderscore($method);
        $parameterName = preg_replace('/^(get|is|set)_/', '', $methodUnderscore);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new ConfigurationException(sprintf('Method "%s::%s()" does not exist.', get_called_class(), $method));
        }
        if (preg_match('/^(get|is)_/', $methodUnderscore)) {
            return $this->values[$parameterName];
        }
        if (empty($arguments)) {
            throw new ConfigurationException(sprintf('Missing argument 1 for "%s::%s()".', get_called_class(), $method));
        }

        $this->values[$parameterName] = reset($arguments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->values;
    }
}
