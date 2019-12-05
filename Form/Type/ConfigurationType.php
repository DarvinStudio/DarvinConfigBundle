<?php declare(strict_types=1);
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015-2019, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Form\Type;

use Darvin\ConfigBundle\Configuration\ConfigurationInterface;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\Utils\Strings\StringsUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Configuration form type
 */
class ConfigurationType extends AbstractType
{
    private const DEFAULT_FIELD_TYPES = [
        ParameterModel::TYPE_ARRAY   => CollectionType::class,
        ParameterModel::TYPE_BOOL    => CheckboxType::class,
        ParameterModel::TYPE_ENTITY  => EntityType::class,
        ParameterModel::TYPE_FLOAT   => NumberType::class,
        ParameterModel::TYPE_INTEGER => IntegerType::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $configuration = $this->getConfiguration($options);

        foreach ($configuration->getModel() as $parameterModel) {
            $options = $parameterModel->getOptions();

            if (isset($options['hidden']) && $options['hidden']) {
                continue;
            }

            $builder->add(
                lcfirst(StringsUtil::toCamelCase($parameterModel->getName())),
                $this->getFieldType($parameterModel),
                $this->getFieldOptions($parameterModel, $configuration)
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('configuration')
            ->setAllowedTypes('configuration', ConfigurationInterface::class)
            ->setDefaults([
                'csrf_token_id' => md5(__FILE__.$this->getBlockPrefix()),
                'data_class'    => function (Options $options) {
                    return get_class($options['configuration']);
                },
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix(): string
    {
        return 'darvin_config_configuration';
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\ParameterModel $parameterModel Parameter model
     *
     * @return string|null
     */
    private function getFieldType(ParameterModel $parameterModel): ?string
    {
        $parameterOptions = $parameterModel->getOptions();

        if (isset($parameterOptions['form']['type'])) {
            return $parameterOptions['form']['type'];
        }
        if (isset(self::DEFAULT_FIELD_TYPES[$parameterModel->getType()])) {
            return self::DEFAULT_FIELD_TYPES[$parameterModel->getType()];
        }

        return null;
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\ParameterModel             $parameterModel Parameter model
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration  Configuration
     *
     * @return array
     */
    private function getFieldOptions(ParameterModel $parameterModel, ConfigurationInterface $configuration): array
    {
        $fieldOptions = [
            'required' => false,
        ];

        $parameterOptions = $parameterModel->getOptions();

        if (isset($parameterOptions['form']['options'])) {
            $fieldOptions = array_merge($fieldOptions, $parameterOptions['form']['options']);
        }
        if (!array_key_exists('label', $fieldOptions)) {
            $fieldOptions['label'] = sprintf(
                'configuration.%s.parameter.%s',
                $configuration->getName(),
                $parameterModel->getName()
            );
        }
        if (ParameterModel::TYPE_ENTITY === $parameterModel->getType() && !isset($fieldOptions['class'])) {
            $fieldOptions['class'] = $parameterOptions['class'];
        }
        if (ParameterModel::TYPE_OBJECT === $parameterModel->getType()
            || (ParameterModel::TYPE_ARRAY === $parameterModel->getType() && is_object($fieldOptions['prototype_data'] ?? null))
        ) {
            if (!isset($fieldOptions['constraints'])) {
                $fieldOptions['constraints'] = [];
            }
            if (!is_array($fieldOptions['constraints'])) {
                $fieldOptions['constraints'] = [$fieldOptions['constraints']];
            }

            $fieldOptions['constraints'][] = new Valid();
        }

        return $fieldOptions;
    }

    /**
     * @param array $options Form options
     *
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface
     */
    private function getConfiguration(array $options): ConfigurationInterface
    {
        return $options['configuration'];
    }
}
