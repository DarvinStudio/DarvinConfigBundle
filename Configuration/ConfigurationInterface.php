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

/**
 * Configuration
 */
interface ConfigurationInterface
{
    /**
     * @param string $method    Method name
     * @param array  $arguments Arguments
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $arguments = []);

    /**
     * @param string $parameterName Configuration parameter name
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get(string $parameterName);

    /**
     * @param string $parameterName  Parameter name
     * @param mixed  $parameterValue Parameter value
     *
     * @throws \InvalidArgumentException
     */
    public function __set(string $parameterName, $parameterValue): void;

    /**
     * @return \Darvin\ConfigBundle\Parameter\ParameterModel[]|iterable
     */
    public function getModel(): iterable;

    /**
     * Saves configuration.
     */
    public function save(): void;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return string
     */
    public function getName(): string;
}
