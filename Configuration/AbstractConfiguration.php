<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
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
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function setConfigurationPool(ConfigurationPool $configurationPool): void
    {
        $this->configurationPool = $configurationPool;
    }

    /**
     * @param string $method    Method name
     * @param array  $arguments Arguments
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $arguments = [])
    {
        $this->configurationPool->init();

        $methodUnderscore = StringsUtil::toUnderscore($method);
        $parameterName = preg_replace('/^(get|is|set)_/', '', $methodUnderscore);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s()" does not exist.', get_called_class(), $method));
        }
        if ($methodUnderscore === $parameterName || preg_match('/^(get|is)_/', $methodUnderscore)) {
            return $this->values[$parameterName];
        }
        if (empty($arguments)) {
            throw new \InvalidArgumentException(sprintf('Missing argument 1 for "%s::%s()".', get_called_class(), $method));
        }

        $this->values[$parameterName] = reset($arguments);

        return $this;
    }

    /**
     * @param string $parameterName  Parameter name
     * @param mixed  $parameterValue Parameter value
     *
     * @throws \InvalidArgumentException
     */
    public function __set(string $parameterName, $parameterValue): void
    {
        $this->configurationPool->init();

        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new \InvalidArgumentException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        $this->values[$parameterName] = $parameterValue;
    }

    /**
     * @param string $parameterName Configuration parameter name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get(string $parameterName)
    {
        $this->configurationPool->init();

        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $this->values)) {
            throw new \InvalidArgumentException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        return $this->values[$parameterName];
    }

    /**
     * {@inheritdoc}
     */
    public function save(): void
    {
        $this->configurationPool->save($this);
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
