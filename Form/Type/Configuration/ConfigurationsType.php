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

use Darvin\ConfigBundle\Configuration\ConfigurationPool;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Configurations form type
 */
class ConfigurationsType extends AbstractType
{
    /**
     * @var \Darvin\ConfigBundle\Configuration\ConfigurationPool
     */
    private $configurationPool;

    /**
     * @param \Darvin\ConfigBundle\Configuration\ConfigurationPool $configurationPool Configuration pool
     */
    public function __construct(ConfigurationPool $configurationPool)
    {
        $this->configurationPool = $configurationPool;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->configurationPool->getAll() as $configuration) {
            $builder->add($configuration->getName(), new ConfigurationType($configuration), array(
                'label' => sprintf('configuration.%s.title', $configuration->getName()),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class'         => get_class($this->configurationPool),
            'intention'          => md5(__FILE__),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'darvin_config_configurations';
    }
}
