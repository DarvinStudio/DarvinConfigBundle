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
    public function save()
    {
        $parameters = array();

        $valueConverter = new ParameterValueConverter();

        foreach ($this->configurations as $configurationName => $configuration) {
            $values = $configuration->getValues();

            foreach ($configuration->getModel() as $parameterModel) {
                $parameterName = $parameterModel->getName();
                $parameterType = $parameterModel->getType();

                $value = array_key_exists($parameterName, $values)
                    ? $values[$parameterName]
                    : $parameterModel->getDefaultValue();
                $value = $valueConverter->toString($value, $parameterType);

                $parameters[] = new Parameter($configurationName, $parameterName, $parameterType, $value);
            }
        }

        $this->parameterRepository->save($parameters);
    }

    private function init()
    {
        if ($this->initialized) {
            return;
        }
        foreach ($this->configurations as $configuration) {
            $this->initConfiguration($configuration);
        }

        $this->initialized = true;
    }

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration Configuration to initialize
     */
    private function initConfiguration(ConfigurationInterface $configuration)
    {
        $values = array();

        foreach ($configuration->getModel() as $parameterModel) {
            $values[$parameterModel->getName()] = $parameterModel->getDefaultValue();
        }

        $configuration->setValues($values);
    }
}
