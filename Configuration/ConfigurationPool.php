<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 15:55
 */

namespace Darvin\ConfigBundle\Configuration;

use Darvin\ConfigBundle\Parameter\Parameter;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\ConfigBundle\Parameter\ParameterValueConverter;
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
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * @var bool
     */
    private $initialized;

    /**
     * @param \Darvin\ConfigBundle\Repository\ParameterRepositoryInterface $parameterRepository Configuration parameter repository
     */
    public function __construct(ParameterRepositoryInterface $parameterRepository)
    {
        $this->parameterRepository = $parameterRepository;
        $this->configurations = array();
        $this->initialized = false;
    }

    /**
     * @param string $configurationName Configuration name
     *
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function __get($configurationName)
    {
        if (!isset($this->configurations[$configurationName])) {
            throw new ConfigurationException(sprintf('Configuration "%s" does not exist.', $configurationName));
        }

        return $this->configurations[$configurationName];
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     *
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    public function add(ConfigurationInterface $configuration)
    {
        if (isset($this->configurations[$configuration->getName()])) {
            throw new ConfigurationException(sprintf('Configuration "%s" already added.', $configuration->getName()));
        }

        $this->validateConfiguration($configuration);

        $this->configurations[$configuration->getName()] = $configuration;
    }

    /**
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface[]
     */
    public function getAll()
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

        $parameters = array();

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

        $parameters = array();

        foreach ($this->parameterRepository->getAll() as $parameter) {
            $configurationName = $parameter->getConfigurationName();

            if (!isset($parameters[$configurationName])) {
                $parameters[$configurationName] = array();
            }

            $parameters[$configurationName][$parameter->getName()] = $parameter;
        }
        foreach ($this->configurations as $configuration) {
            $configurationName = $configuration->getName();
            $this->initConfiguration(
                $configuration,
                isset($parameters[$configurationName]) ? $parameters[$configurationName] : array()
            );
        }

        $this->initialized = true;
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     *
     * @return array
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    private function getConfigurationParameters(ConfigurationInterface $configuration)
    {
        $parameters = array();

        $values = $configuration->getValues();

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $parameterType = $parameterModel->getType();

            if (array_key_exists($parameterName, $values)) {
                $value = $values[$parameterName];

                if (null !== $value && gettype($value) !== $parameterType) {
                    $message = sprintf(
                        'Parameter "%s" of configuration "%s" must have value of "%s" type, "%s" type value provided.',
                        $parameterName,
                        $configuration->getName(),
                        $parameterType,
                        gettype($value)
                    );

                    throw new ConfigurationException($message);
                }
            } else {
                $value = $parameterModel->getDefaultValue();
            }

            $value = ParameterValueConverter::toString($value, $parameterType);

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
        $values = array();

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();

            if (!isset($parameters[$parameterName])) {
                $values[$parameterName] = $parameterModel->getDefaultValue();

                continue;
            }

            $parameter = $parameters[$parameterName];
            $values[$parameterName] = ParameterValueConverter::fromString($parameter->getValue(), $parameterModel->getType());
        }

        $configuration->setValues($values);
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to validate
     *
     * @throws \Darvin\ConfigBundle\Configuration\ConfigurationException
     */
    private function validateConfiguration(ConfigurationInterface $configuration)
    {
        $parameterNames = array();

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $parameterType = $parameterModel->getType();
            $parameterDefaultValue = $parameterModel->getDefaultValue();

            if (null !== $parameterDefaultValue && gettype($parameterDefaultValue) !== $parameterType) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" must have default value of "%s" type, "%s" type value provided.',
                    $parameterName,
                    $configuration->getName(),
                    $parameterType,
                    gettype($parameterDefaultValue)
                );

                throw new ConfigurationException($message);
            }
            if (!ParameterModel::isTypeExists($parameterType)) {
                $message = sprintf(
                    'Type "%s" of "%s" configuration parameter "%s" is not supported. Supported types: "%s".',
                    $parameterType,
                    $configuration->getName(),
                    $parameterName,
                    implode('", "', ParameterModel::getTypes())
                );

                throw new ConfigurationException($message);
            }
            if (in_array($parameterName, $parameterNames)) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" already defined.',
                    $parameterName,
                    $configuration->getName()
                );

                throw new ConfigurationException($message);
            }

            $parameterNames[] = $parameterName;
        }
    }
}
