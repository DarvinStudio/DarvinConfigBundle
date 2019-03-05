# DarvinConfigBundle
This bundle provides configuration layer for Symfony-based applications.

## Usage

Create class which implements Darvin\ConfigBundle\Configuration\ConfigurationInterface
 or extends Darvin\ConfigBundle\Configuration\AbstractConfiguration:

```php
// AppBundle\Config\AppConfig.php

use Darvin\ConfigBundle\Configuration\AbstractConfiguration;
use Darvin\ConfigBundle\Parameter\ParameterModel;

class AppConfig extends AbstractConfiguration
{
    public function getModel(): iterable
    {
        yield new ParameterModel('array_var', ParameterModel::TYPE_ARRAY, array(1, 2, 3));
        yield new ParameterModel('boolean_var', ParameterModel::TYPE_BOOL, false);
        yield new ParameterModel('integer_var', ParameterModel::TYPE_INTEGER, 777);
        yield new ParameterModel('string_var', ParameterModel::TYPE_STRING, 'test123');
    }
}
```

Define created class as a service tagged with "darvin_config.configuration" tag:

```yaml
services:
    app.config:
        class: AppBundle\Config\AppConfig
        tags:
            - { name: darvin_config.configuration }
```
