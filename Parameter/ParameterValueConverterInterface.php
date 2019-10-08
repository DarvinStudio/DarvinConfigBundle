<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Parameter;

/**
 * Configuration parameter value converter
 */
interface ParameterValueConverterInterface
{
    /**
     * @param string $value   Parameter value
     * @param string $type    Parameter type
     * @param array  $options Parameter options
     *
     * @return mixed
     */
    public function fromString(string $value, string $type, array $options);

    /**
     * @param mixed  $value   Parameter value
     * @param string $type    Parameter type
     * @param array  $options Parameter options
     *
     * @return string|null
     */
    public function toString($value, string $type, array $options): ?string;
}
