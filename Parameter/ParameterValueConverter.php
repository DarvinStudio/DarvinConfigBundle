<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 14.08.15
 * Time: 10:32
 */

namespace Darvin\ConfigBundle\Parameter;

/**
 * Configuration parameter value converter
 */
class ParameterValueConverter
{
    /**
     * @var array
     */
    private static $fromStringCallbacks = array(
        ParameterModel::TYPE_ARRAY   => 'json_decode',
        ParameterModel::TYPE_BOOL    => 'boolval',
        ParameterModel::TYPE_FLOAT   => 'floatval',
        ParameterModel::TYPE_INTEGER => 'intval',
    );

    /**
     * @var array
     */
    private static $toStringCallbacks = array(
        ParameterModel::TYPE_ARRAY   => 'json_encode',
        ParameterModel::TYPE_BOOL    => 'strval',
        ParameterModel::TYPE_FLOAT   => 'strval',
        ParameterModel::TYPE_INTEGER => 'strval',
    );

    /**
     * @param string $value Parameter value
     * @param string $type  Parameter type
     *
     * @return mixed
     */
    public static function fromString($value, $type)
    {
        return self::convert($value, $type, self::$fromStringCallbacks);
    }

    /**
     * @param mixed  $value Parameter value
     * @param string $type  Parameter type
     *
     * @return string
     */
    public static function toString($value, $type)
    {
        return self::convert($value, $type, self::$toStringCallbacks);
    }

    /**
     * @param mixed  $value            Parameter value
     * @param string $type             Parameter type
     * @param array  $convertCallbacks Convert callbacks
     *
     * @return mixed
     */
    private static function convert($value, $type, array $convertCallbacks)
    {
        if (!isset($convertCallbacks[$type])) {
            return $value;
        }

        $callback = $convertCallbacks[$type];

        return $callback($value);
    }
}
