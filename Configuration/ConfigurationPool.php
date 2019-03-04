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

use Darvin\ConfigBundle\Parameter\ParameterModel;

/**
 * Configuration pool
 */
class ConfigurationPool
{
    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Configuration pool constructor.
     */
    public function __construct()
    {
        $this->configurations = [];
    }

    /**
     * @param string $configurationName Configuration name
     *
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface
     * @throws \InvalidArgumentException
     */
    public function __get($configurationName)
    {
        if (!isset($this->configurations[$configurationName])) {
            throw new \InvalidArgumentException(sprintf('Configuration "%s" does not exist.', $configurationName));
        }

        return $this->configurations[$configurationName];
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     *
     * @throws \InvalidArgumentException
     */
    public function addConfiguration(ConfigurationInterface $configuration)
    {
        if (isset($this->configurations[$configuration->getName()])) {
            throw new \InvalidArgumentException(sprintf('Configuration "%s" already added.', $configuration->getName()));
        }

        $this->validateConfiguration($configuration);

        $this->configurations[$configuration->getName()] = $configuration;
    }

    /**
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    public function getAllConfigurations()
    {
        return $this->configurations;
    }

    /**
     * Saves configurations.
     */
    public function saveAll()
    {
        foreach ($this->configurations as $configuration) {
            $configuration->save();
        }
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to validate
     *
     * @throws \UnexpectedValueException
     */
    private function validateConfiguration(ConfigurationInterface $configuration)
    {
        $parameterNames = [];

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $parameterType = $parameterModel->getType();
            $parameterDefaultValue = $parameterModel->getDefaultValue();
            $parameterDataType = $parameterModel->getDataType();

            if (null !== $parameterDefaultValue && gettype($parameterDefaultValue) !== $parameterDataType) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" must have default value of "%s" type, "%s" type value provided.',
                    $parameterName,
                    $configuration->getName(),
                    $parameterDataType,
                    gettype($parameterDefaultValue)
                );

                throw new \UnexpectedValueException($message);
            }
            if (!ParameterModel::typeExists($parameterType)) {
                $message = sprintf(
                    'Type "%s" of "%s" configuration parameter "%s" is not supported. Supported types: "%s".',
                    $parameterType,
                    $configuration->getName(),
                    $parameterName,
                    implode('", "', ParameterModel::TYPES)
                );

                throw new \UnexpectedValueException($message);
            }
            if (in_array($parameterName, $parameterNames)) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" already defined.',
                    $parameterName,
                    $configuration->getName()
                );

                throw new \UnexpectedValueException($message);
            }

            $parameterNames[] = $parameterName;
        }
    }
}
