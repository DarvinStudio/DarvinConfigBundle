<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 15:55
 */

namespace Darvin\ConfigBundle\Configuration;

use Darvin\ConfigBundle\Parameter\Parameter;
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
     * Saves configurations.
     */
    public function save()
    {
        $parameters = array();

        foreach ($this->configurations as $configuration) {
            $parameters = array_merge($parameters, $this->getParameters($configuration));
        }

        $this->parameterRepository->save($parameters);
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to save
     */
    public function saveConfiguration(ConfigurationInterface $configuration)
    {
        $this->parameterRepository->save($this->getParameters($configuration));
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration
     *
     * @return array
     */
    private function getParameters(ConfigurationInterface $configuration)
    {
        $parameters = array();

        $values = $configuration->getValues();

        foreach ($configuration->getModel() as $parameterModel) {
            $parameterName = $parameterModel->getName();
            $parameterType = $parameterModel->getType();

            $value = array_key_exists($parameterName, $values)
                ? $values[$parameterName]
                : $parameterModel->getDefaultValue();
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

            if (isset($parameters[$parameterName])) {
                $parameter = $parameters[$parameterName];
                $value = $parameter->getValue();
            } else {
                $value = $parameterModel->getDefaultValue();
            }

            $values[$parameterName] = ParameterValueConverter::fromString($value, $parameterModel->getType());
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
