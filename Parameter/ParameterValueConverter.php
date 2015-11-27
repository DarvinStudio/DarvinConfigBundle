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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;

/**
 * Configuration parameter value converter
 */
class ParameterValueConverter
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $om;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $om Object manager
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param string $value   Parameter value
     * @param string $type    Parameter type
     * @param array  $options Parameter options
     *
     * @return mixed
     */
    public function fromString($value, $type, array $options)
    {
        return $this->convert($value, $type, $options, $this->getFromStringCallbacks());
    }

    /**
     * @param mixed  $value   Parameter value
     * @param string $type    Parameter type
     * @param array  $options Parameter options
     *
     * @return string
     */
    public function toString($value, $type, array $options)
    {
        return $this->convert($value, $type, $options, $this->getToStringCallbacks());
    }

    /**
     * @param mixed  $value            Parameter value
     * @param string $type             Parameter type
     * @param array  $options          Parameter options
     * @param array  $convertCallbacks Convert callbacks
     *
     * @return mixed
     */
    private function convert($value, $type, array $options, array $convertCallbacks)
    {
        if (null === $value) {
            return ParameterModel::TYPE_STRING === $type ? '' : null;
        }
        if (!isset($convertCallbacks[$type])) {
            return $value;
        }

        $callback = $convertCallbacks[$type];

        return is_string($callback) ? $callback($value) : $callback($value, $options);
    }

    /**
     * @return array
     */
    private function getFromStringCallbacks()
    {
        $om = $this->om;

        return array(
            ParameterModel::TYPE_ARRAY   => 'unserialize',
            ParameterModel::TYPE_BOOL    => 'boolval',
            ParameterModel::TYPE_ENTITY  => function ($id, $options) use ($om) {
                return !empty($id) ? $om->find($options['class'], $id) : null;
            },
            ParameterModel::TYPE_FLOAT   => 'floatval',
            ParameterModel::TYPE_INTEGER => 'intval',
        );
    }

    /**
     * @return array
     */
    private function getToStringCallbacks()
    {
        $om = $this->om;

        return array(
            ParameterModel::TYPE_ARRAY   => 'serialize',
            ParameterModel::TYPE_BOOL    => 'strval',
            ParameterModel::TYPE_ENTITY  => function ($entity) use ($om) {
                if (empty($entity)) {
                    return '';
                }

                $ids = $om->getClassMetadata(ClassUtils::getClass($entity))->getIdentifierValues($entity);

                return (string) reset($ids);
            },
            ParameterModel::TYPE_FLOAT   => 'strval',
            ParameterModel::TYPE_INTEGER => 'strval',
        );
    }
}
