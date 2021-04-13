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

use Darvin\ConfigBundle\Parameter\Parameter;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\ConfigBundle\Parameter\ParameterValueConverterInterface;
use Darvin\ConfigBundle\Repository\ParameterRepositoryInterface;
use Darvin\Utils\Strings\StringsUtil;

/**
 * Configuration abstract implementation
 */
abstract class AbstractConfiguration implements ConfigurationInterface
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
     * @var string|null
     */
    private $name;

    /**
     * @var \Darvin\ConfigBundle\Parameter\Parameter[]|null
     */
    private $persistedParameters = null;

    /**
     * @var array|null
     */
    private $values = null;

    /**
     * @param \Darvin\ConfigBundle\Repository\ParameterRepositoryInterface $parameterRepository Parameter repository
     */
    public function setParameterRepository(ParameterRepositoryInterface $parameterRepository): void
    {
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\ParameterValueConverterInterface $parameterValueConverter Parameter value converter
     */
    public function setParameterValueConverter(ParameterValueConverterInterface $parameterValueConverter): void
    {
        $this->parameterValueConverter = $parameterValueConverter;
    }

    /**
     * {@inheritDoc}
     */
    public function __call(string $method, array $arguments = [])
    {
        $values           = $this->getValues();
        $methodUnderscore = StringsUtil::toUnderscore($method);

        $parameterName = preg_replace('/^(get|is|set)_/', '', $methodUnderscore);

        if (!array_key_exists($parameterName, $values)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s()" does not exist.', get_called_class(), $method));
        }
        if ($methodUnderscore === $parameterName || preg_match('/^(get|is)_/', $methodUnderscore)) {
            return $values[$parameterName];
        }
        if (empty($arguments)) {
            throw new \InvalidArgumentException(sprintf('Missing argument 1 for "%s::%s()".', get_called_class(), $method));
        }

        $values[$parameterName] = reset($arguments);

        $this->values = $values;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __set(string $parameterName, $parameterValue): void
    {
        $values        = $this->getValues();
        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $values)) {
            throw new \InvalidArgumentException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        $values[$parameterName] = $parameterValue;

        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     */
    public function __get(string $parameterName)
    {
        $values        = $this->getValues();
        $parameterName = StringsUtil::toUnderscore($parameterName);

        if (!array_key_exists($parameterName, $values)) {
            throw new \InvalidArgumentException(
                sprintf('Parameter "%s" is not defined in configuration "%s".', $parameterName, $this->getName())
            );
        }

        return $values[$parameterName];
    }

    /**
     * {@inheritDoc}
     */
    public function save(): void
    {
        $names      = [];
        $parameters = $this->getParametersToSave();

        foreach ($parameters as $parameter) {
            $names[$parameter->getName()] = $parameter->getName();
        }
        foreach ($this->getModel() as $parameterModel) {
            if (!isset($names[$parameterModel->getName()])) {
                $this->values[$parameterModel->getName()] = $parameterModel->getDefaultValue();
            }
        }

        $this->parameterRepository->saveConfigurationParameters($this->getName(), $parameters);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        if (null === $this->name) {
            $this->name = StringsUtil::toUnderscore(preg_replace('/^.*\\\|(Config|Configuration)$/', '', get_class($this)));
        }

        return $this->name;
    }

    /**
     * @return array
     */
    private function getValues(): array
    {
        if (null === $this->values) {
            $values              = [];
            $persistedParameters = $this->getPersistedParameters();

            foreach ($this->getModel() as $parameterModel) {
                $parameterName = $parameterModel->getName();
                $default       = $parameterModel->getDefaultValue();

                if (!isset($persistedParameters[$parameterName])) {
                    $values[$parameterName] = $default;

                    continue;
                }

                $parameter = $persistedParameters[$parameterName];

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

            $this->values = $values;
        }

        return $this->values;
    }

    /**
     * @return \Darvin\ConfigBundle\Parameter\Parameter[]
     * @throws \UnexpectedValueException
     */
    private function getParametersToSave(): array
    {
        $parameters = [];
        $values     = $this->getValues();

        foreach ($this->getModel() as $parameterModel) {
            $parameterName     = $parameterModel->getName();
            $parameterType     = $parameterModel->getType();
            $parameterDataType = $parameterModel->getDataType();

            if (!array_key_exists($parameterName, $values)) {
                continue;
            }

            $value = $values[$parameterName];

            if (null === $value || $this->isDefault($value, $parameterModel)) {
                continue;
            }
            if (gettype($value) !== $parameterDataType) {
                $message = sprintf(
                    'Parameter "%s" of configuration "%s" must have value of "%s" type, "%s" type value provided.',
                    $parameterName,
                    $this->getName(),
                    $parameterDataType,
                    gettype($value)
                );

                throw new \UnexpectedValueException($message);
            }

            $value = $this->parameterValueConverter->toString($value, $parameterType, $parameterModel->getOptions());

            $parameters[] = new Parameter($this->getName(), $parameterName, $parameterType, $value);
        }

        return $parameters;
    }

    /**
     * @param mixed                                         $value          Value
     * @param \Darvin\ConfigBundle\Parameter\ParameterModel $parameterModel Parameter model
     *
     * @return bool
     */
    private function isDefault($value, ParameterModel $parameterModel): bool
    {
        if (is_scalar($value)) {
            return $value === $parameterModel->getDefaultValue();
        }

        return $value == $parameterModel->getDefaultValue();
    }

    /**
     * @return \Darvin\ConfigBundle\Parameter\Parameter[]
     */
    private function getPersistedParameters(): array
    {
        if (null === $this->persistedParameters) {
            $parameters = [];

            foreach ($this->parameterRepository->getConfigurationParameters($this->getName()) as $parameter) {
                $parameters[$parameter->getName()] = $parameter;
            }

            $this->persistedParameters = $parameters;
        }

        return $this->persistedParameters;
    }
}
