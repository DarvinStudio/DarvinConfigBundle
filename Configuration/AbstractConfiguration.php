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

use Darvin\Utils\Strings\StringsUtil;

/**
 * Configuration abstract implementation
 */
abstract class AbstractConfiguration implements ConfigurationInterface
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @param string $method    Method name
     * @param array  $arguments Arguments
     *
     * @return mixed
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function __call($method, array $arguments = [])
    {
        $this->configurationPool->init();

        $methodUnderscore = StringsUtil::toUnderscore($method);
        $parameterName = preg_replace('/^(get|is|set)_/', '', $methodUnderscore);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new ConfigurationException(sprintf('Method "%s::%s()" does not exist.', get_called_class(), $method));
        }
        if ($methodUnderscore === $parameterName || preg_match('/^(get|is)_/', $methodUnderscore)) {
            return $this->values[$parameterName];
        }
        if (empty($arguments)) {
            throw new ConfigurationException(sprintf('Missing argument 1 for "%s::%s()".', get_called_class(), $method));
        }

        $this->values[$parameterName] = reset($arguments);

        return $this;
    }

    /**
     * @param string $parameterName  Parameter name
     * @param mixed  $parameterValue Parameter value
     *
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function __set($parameterName, $parameterValue)
    {
        $this->configurationPool->init();

        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new ConfigurationException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        $this->values[$parameterName] = $parameterValue;
    }

    /**
     * @param string $parameterName Configuration parameter name
     *
     * @return mixed
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function __get($parameterName)
    {
        $this->configurationPool->init();

        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new ConfigurationException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        return $this->values[$parameterName];
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigurationPool(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->configurationPool->save($this);
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
