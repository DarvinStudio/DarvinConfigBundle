# DarvinConfigBundle
This bundle provides configuration layer for Symfony2-based applications.

## Usage

Create class which implements Darvin\ConfigBundle\Configuration\ConfigurationInterface
 or extends Darvin\ConfigBundle\Configuration\AbstractConfiguration:

```php
// AppBundle\Configuration\Configuration.php

use Darvin\ConfigBundle\Configuration\AbstractConfiguration;
use Darvin\ConfigBundle\Parameter\ParameterModel;

class Configuration extends AbstractConfiguration
{
    public function getModel()
    {
        return array(
            new ParameterModel('array_var', ParameterModel::TYPE_ARRAY, array(1, 2, 3)),
            new ParameterModel('boolean_var', ParameterModel::TYPE_BOOL, false),
            new ParameterModel('integer_var', ParameterModel::TYPE_INTEGER, 777),
            new ParameterModel('string_var', ParameterModel::TYPE_STRING, 'test123'),
        );
    }

    public function getName()
    {
        return 'app_configuration';
    }
}
```

Define created class as a service tagged with "darvin_config.configuration" tag:

```yaml
services:
    app.configuration:
        class: AppBundle\Configuration\Configuration
        tags:
            - { name: darvin_config.configuration }
```
