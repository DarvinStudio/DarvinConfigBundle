<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
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
    const TYPE_ARRAY   = 'array';
    const TYPE_BOOL    = 'bool';
    const TYPE_ENTITY  = 'entity';
    const TYPE_FLOAT   = 'float';
    const TYPE_INTEGER = 'integer';
    const TYPE_OBJECT  = 'object';
    const TYPE_STRING  = 'string';

    /**
     * @var array
     */
    private static $dataTypes = [
        self::TYPE_ARRAY   => 'array',
        self::TYPE_BOOL    => 'boolean',
        self::TYPE_ENTITY  => 'object',
        self::TYPE_FLOAT   => 'double',
        self::TYPE_INTEGER => 'integer',
        self::TYPE_OBJECT  => 'object',
        self::TYPE_STRING  => 'string',
    ];

    /**
     * @var array
     */
    private static $requiredOptions = [
        self::TYPE_ENTITY => [
            'class',
        ],
        self::TYPE_OBJECT => [
            'class',
        ],
    ];

    /**
     * @var array
     */
    private static $types = [
        self::TYPE_ARRAY,
        self::TYPE_BOOL,
        self::TYPE_ENTITY,
        self::TYPE_FLOAT,
        self::TYPE_INTEGER,
        self::TYPE_OBJECT,
        self::TYPE_STRING,
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
    public function __construct($name, $type = self::TYPE_STRING, $defaultValue = null, array $options = [])
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
    public function getDataType()
    {
        return self::$dataTypes[$this->type];
    }

    /**
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return ParameterModel
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param array $options Options
     *
     * @return ParameterModel
     */
    public function setOptions(array $options)
    {
        $this->validateOptions($options, $this->type);

        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return self::$types;
    }

    /**
     * @param string $type Type
     *
     * @return bool
     */
    public static function isTypeExists($type)
    {
        return in_array($type, self::$types);
    }

    /**
     * @param string $name name
     *
     * @return ParameterModel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type type
     *
     * @return ParameterModel
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $defaultValue defaultValue
     *
     * @return ParameterModel
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array  $options Options
     * @param string $type    Parameter type
     *
     * @throws \LogicException
     */
    private function validateOptions(array $options, $type)
    {
        if (!isset(self::$requiredOptions[$type])) {
            return;
        }
        foreach (self::$requiredOptions[$type] as $requiredOption) {
            if (!array_key_exists($requiredOption, $options)) {
                throw new \LogicException(
                    sprintf('Option "%s" must be provided for "%s" type parameters.', $requiredOption, $type)
                );
            }
        }
    }
}
