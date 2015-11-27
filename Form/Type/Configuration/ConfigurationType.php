<?php
/**
 * @author    Igor Nikolaev <igor.sv.n@gmail.com>
 * @copyright Copyright (c) 2015, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\ConfigBundle\Form\Type\Configuration;

use Darvin\ConfigBundle\Configuration\ConfigurationInterface;
use Darvin\ConfigBundle\Parameter\ParameterModel;
use Darvin\Utils\Strings\StringsUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Configuration form type
 */
class ConfigurationType extends AbstractType
{
    const CONFIGURATION_TYPE_CLASS = __CLASS__;

    /**
     * @var array
     */
    private static $defaultFieldOptions = array(
        ParameterModel::TYPE_ARRAY => array(
            'allow_add'    => true,
            'allow_delete' => true,
        ),
    );

    /**
     * @var array
     */
    private static $defaultFieldTypes = array(
        ParameterModel::TYPE_ARRAY   => 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
        ParameterModel::TYPE_BOOL    => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
        ParameterModel::TYPE_ENTITY  => 'Symfony\Bridge\Doctrine\Form\Type\EntityType',
        ParameterModel::TYPE_FLOAT   => 'Symfony\Component\Form\Extension\Core\Type\NumberType',
        ParameterModel::TYPE_INTEGER => 'Symfony\Component\Form\Extension\Core\Type\IntegerType',
    );

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->getConfiguration($options);

        foreach ($configuration->getModel() as $parameterModel) {
            $builder->add(
                lcfirst(StringsUtil::toCamelCase($parameterModel->getName())),
                $this->getFieldType($parameterModel),
                $this->getFieldOptions($parameterModel, $configuration)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'intention' => md5(__FILE__.$this->getBlockPrefix()),
            ))
            ->remove('data_class')
            ->setRequired(array(
                'configuration',
                'data_class',
            ))
            ->setAllowedTypes('configuration', ConfigurationInterface::CONFIGURATION_INTERFACE)
            ->setAllowedTypes('data_class', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'darvin_config_configuration';
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\ParameterModel $parameterModel Parameter model
     *
     * @return string
     */
    private function getFieldType(ParameterModel $parameterModel)
    {
        $parameterOptions = $parameterModel->getOptions();

        if (isset($parameterOptions['form']['type'])) {
            return $parameterOptions['form']['type'];
        }
        if (isset(self::$defaultFieldTypes[$parameterModel->getType()])) {
            return self::$defaultFieldTypes[$parameterModel->getType()];
        }

        return null;
    }

    /**
     * @param \Darvin\ConfigBundle\Parameter\ParameterModel             $parameterModel Parameter model
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationInterface $configuration  Configuration
     *
     * @return array
     */
    private function getFieldOptions(ParameterModel $parameterModel, ConfigurationInterface $configuration)
    {
        $fieldOptions = array(
            'required' => false,
        );

        if (isset(self::$defaultFieldOptions[$parameterModel->getType()])) {
            $fieldOptions = array_merge($fieldOptions, self::$defaultFieldOptions[$parameterModel->getType()]);
        }

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

        return $fieldOptions;
    }

    /**
     * @param array $options Form options
     *
     * @return \Darvin\ConfigBundle\Configuration\ConfigurationInterface
     */
    private function getConfiguration(array $options)
    {
        return $options['configuration'];
    }
}
