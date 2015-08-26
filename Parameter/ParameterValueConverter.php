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

/**
 * Configuration parameter value converter
 */
class ParameterValueConverter
{
    /**
     * @param string $value Parameter value
     * @param string $type  Parameter type
     *
     * @return mixed
     */
    public function fromString($value, $type)
    {
        return $this->convert($value, $type, self::getFromStringCallbacks());
    }

    /**
     * @param mixed  $value Parameter value
     * @param string $type  Parameter type
     *
     * @return string
     */
    public function toString($value, $type)
    {
        return $this->convert($value, $type, self::getToStringCallbacks());
    }

    /**
     * @param mixed  $value            Parameter value
     * @param string $type             Parameter type
     * @param array  $convertCallbacks Convert callbacks
     *
     * @return mixed
     */
    private function convert($value, $type, array $convertCallbacks)
    {
        if (null === $value) {
            return $value;
        }
        if (!isset($convertCallbacks[$type])) {
            return $value;
        }

        $callback = $convertCallbacks[$type];

        return $callback($value);
    }

    /**
     * @return array
     */
    private static function getFromStringCallbacks()
    {
        return array(
            ParameterModel::TYPE_ARRAY   => 'unserialize',
            ParameterModel::TYPE_BOOL    => 'boolval',
            ParameterModel::TYPE_FLOAT   => 'floatval',
            ParameterModel::TYPE_INTEGER => 'intval',
        );
    }

    /**
     * @return array
     */
    private static function getToStringCallbacks()
    {
        return array(
            ParameterModel::TYPE_ARRAY   => 'serialize',
            ParameterModel::TYPE_BOOL    => 'strval',
            ParameterModel::TYPE_FLOAT   => 'strval',
            ParameterModel::TYPE_INTEGER => 'strval',
        );
    }
}
