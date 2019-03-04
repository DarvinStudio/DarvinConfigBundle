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

use Darvin\ConfigBundle\Parameter\Parameter;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\ConfigBundle\Parameter\ParameterValueConverterInterface;
use Darvin\ConfigBundle\Repository\ParameterRepositoryInterface;

/**
 * Configuration pool
 */
class ConfigurationPool
{
    /**
     * @var \Darvin\ConfigBundle\Repository\ParameterRepositoryInterface
     */
    private $parameterRepository;

    /**
     * @var \Darvin\ConfigBundle\Parameter\ParameterValueConverterInterface
     */
    private $parameterValueConverter;

    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * @var \Darvin\ConfigBundle\Parameter\Parameter[]
     */
    private $persistedParameters;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Darvin\ConfigBundle\Repository\ParameterRepositoryInterface    $parameterRepository     Configuration parameter repository
     * @param \Darvin\ConfigBundle\Parameter\ParameterValueConverterInterface $parameterValueConverter Parameter value converter
     */
    public function __construct(ParameterRepositoryInterface $parameterRepository, ParameterValueConverterInterface $parameterValueConverter)
    {
        $this->parameterRepository = $parameterRepository;
        $this->parameterValueConverter = $parameterValueConverter;
        $this->configurations = [];
        $this->initialized = false;
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
        $this->init();

        return $this->configurations;
    }

    /**
     * Saves configurations.
     */
    public function saveAll()
    {
        $this->init();

        $parameters = [];

        foreach ($this->configurations as $configuration) {
            $parameters = array_merge($parameters, $this->getConfigurationParameters($configuration));
        }

        $this->parameterRepository->save($parameters);
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to save
     */
    public function save(ConfigurationInterface $configuration)
    {
        $this->init();

        $this->parameterRepository->save($this->getConfigurationParameters($configuration));
    }

    /**
     * Initializes configurations.
     */
    public function init()
    {
        if ($this->initialized) {
            return;
        }

        $parameters = [];

        foreach ($this->parameterRepository->getAllParameters() as $parameter) {
            $configurationName = $parameter->getConfigurationName();

            if (!isset($parameters[$configurationName])) {
                $parameters[$configurationName] = [];
            }

            $parameters[$configurationName][$parameter->getName()] = $parameter;
        }

        $this->persistedParameters = $parameters;

        foreach ($this->configurations as $configuration) {
            $configurationName = $configuration->getName();
            $this->initConfiguration(
                $configuration,
                isset($parameters[$configurationName]) ? $parameters[$configurationName] : []
            );
        }

        $this->initialized = true;
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     *
     * @return array
     * @throws \UnexpectedValueException
     */
    private function getConfigurationParameters(ConfigurationInterface $configuration)
    {
        $parameters = [];

        $values = $configuration->getValues();

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $parameterType = $parameterModel->getType();
            $parameterDataType = $parameterModel->getDataType();

            if (!array_key_exists($parameterName, $values)) {
                continue;
            }

            $value = $values[$parameterName];

            if ($value == $parameterModel->getDefaultValue()
                && !isset($this->persistedParameters[$configuration->getName()][$parameterName])
            ) {
                continue;
            }
            if (null !== $value && gettype($value) !== $parameterDataType) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" must have value of "%s" type, "%s" type value provided.',
                    $parameterName,
                    $configuration->getName(),
                    $parameterDataType,
                    gettype($value)
                );

                throw new \UnexpectedValueException($message);
            }

            $value = $this->parameterValueConverter->toString($value, $parameterType, $parameterModel->getOptions());

            $parameters[] = new Parameter($configuration->getName(), $parameterName, $parameterType, $value);
        }

        return $parameters;
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to initialize
     * @param \Darvin\ConfigBundle\Parameter\Parameter[]                $parameters    Configuration parameters
     */
    private function initConfiguration(ConfigurationInterface $configuration, array $parameters)
    {
        $values = [];

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $default = $parameterModel->getDefaultValue();

            if (!isset($parameters[$parameterName])) {
                $values[$parameterName] = $default;

                continue;
            }

            $parameter = $parameters[$parameterName];

            if (null === $parameter->getValue()) {
                $values[$parameterName] = $default;

                continue;
            }

            $value = $this->parameterValueConverter->fromString(
                $parameter->getValue(),
                $parameterModel->getType(),
                $parameterModel->getOptions()
            );

            if (ParameterModel::TYPE_ARRAY === $parameterModel->getType()) {
                $valueKeys = array_keys($value);

                if (count($value) !== count($default) && $valueKeys !== array_map('intval', $valueKeys)) {
                    // Add missing elements
                    foreach ($default as $key => $v) {
                        if (!array_key_exists($key, $value)) {
                            $value[$key] = $v;
                        }
                    }

                    $value = array_merge($default, $value);

                    // Remove redundant elements
                    foreach ($value as $key => $v) {
                        if (!array_key_exists($key, $default)) {
                            unset($value[$key]);
                        }
                    }
                }
            }

            $values[$parameterName] = $value;
        }

        $configuration->setValues($values);
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
