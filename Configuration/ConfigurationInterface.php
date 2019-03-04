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
     * @return \Darvin\ConfigBundle\Parameter\ParameterModel[]|iterable
     */
    public function getModel(): iterable;

    /**
     * Saves configuration.
     */
    public function save(): void;

    /**
     * @param array $values Parameter values
     */
    public function setValues(array $values): void;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @return string
     */
    public function getName(): string;
}
