<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Parameter;

use Darvin\Utils\Strings\StringsUtil;

/**
 * Configuration parameter model
 */
class ParameterModel
{
    public const TYPE_ARRAY   = 'array';
    public const TYPE_BOOL    = 'bool';
    public const TYPE_ENTITY  = 'entity';
    public const TYPE_FLOAT   = 'float';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_OBJECT  = 'object';
    public const TYPE_STRING  = 'string';

    public const TYPES = [
        self::TYPE_ARRAY,
        self::TYPE_BOOL,
        self::TYPE_ENTITY,
        self::TYPE_FLOAT,
        self::TYPE_INTEGER,
        self::TYPE_OBJECT,
        self::TYPE_STRING,
    ];

    private const DATA_TYPES = [
        self::TYPE_ARRAY   => 'array',
        self::TYPE_BOOL    => 'boolean',
        self::TYPE_ENTITY  => 'object',
        self::TYPE_FLOAT   => 'double',
        self::TYPE_INTEGER => 'integer',
        self::TYPE_OBJECT  => 'object',
        self::TYPE_STRING  => 'string',
    ];

    private const REQUIRED_OPTIONS = [
        self::TYPE_ENTITY => [
            'class',
        ],
        self::TYPE_OBJECT => [
            'class',
        ],
    ];

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
    private $defaultValue;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $name         Name
     * @param string $type         Type
     * @param mixed  $defaultValue Default value
     * @param array  $options      Options
     */
    public function __construct(string $name, string $type = self::TYPE_STRING, $defaultValue = null, array $options = [])
    {
        $this->validateOptions($options, $type);

        $this->name = StringsUtil::toUnderscore($name);
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return self::DATA_TYPES[$this->type];
    }

    /**
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return ParameterModel
     */
    public function setOption(string $name, $value): ParameterModel
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param array $options Options
     *
     * @return ParameterModel
     */
    public function setOptions(array $options): ParameterModel
    {
        $this->validateOptions($options, $this->type);

        $this->options = $options;

        return $this;
    }

    /**
     * @param string $type Type
     *
     * @return bool
     */
    public static function typeExists(string $type): bool
    {
        return in_array($type, self::TYPES);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name name
     *
     * @return ParameterModel
     */
    public function setName(string $name): ParameterModel
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type type
     *
     * @return ParameterModel
     */
    public function setType(string $type): ParameterModel
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue defaultValue
     *
     * @return ParameterModel
     */
    public function setDefaultValue($defaultValue): ParameterModel
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array  $options Options
     * @param string $type    Parameter type
     *
     * @throws \LogicException
     */
    private function validateOptions(array $options, string $type): void
    {
        if (!isset(self::REQUIRED_OPTIONS[$type])) {
            return;
        }
        foreach (self::REQUIRED_OPTIONS[$type] as $requiredOption) {
            if (!array_key_exists($requiredOption, $options)) {
                throw new \LogicException(
                    sprintf('Option "%s" must be provided for "%s" type parameters.', $requiredOption, $type)
                );
            }
        }
    }
}
