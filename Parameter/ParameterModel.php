<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 13.08.15
 * Time: 16:32
 */

namespace Darvin\ConfigBundle\Parameter;

use Darvin\Utils\Strings\StringsUtil;

/**
 * Configuration parameter model
 */
class ParameterModel
{
    const TYPE_ARRAY   = 'array';
    const TYPE_BOOL    = 'boolean';
    const TYPE_FLOAT   = 'double';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING  = 'string';

    /**
     * @var array
     */
    private static $types = array(
        self::TYPE_ARRAY,
        self::TYPE_BOOL,
        self::TYPE_FLOAT,
        self::TYPE_INTEGER,
        self::TYPE_STRING,
    );

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
     * @param string $name         Name
     * @param string $type         Type
     * @param mixed  $defaultValue Default value
     */
    public function __construct($name, $type, $defaultValue = null)
    {
        $this->name = StringsUtil::toUnderscore($name);
        $this->type = $type;
        $this->defaultValue = $defaultValue;
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
}
