5.1.0: Add "object" parameter type. Usage:

```php
...

new ParameterModel('map', ParameterModel::TYPE_OBJECT, new Map(), [
    'class' => Map::class,
    'form'  => [
        'type' => MapType::class,
    ],
]),

...
```

5.1.1: Sort configurations in the add configurations compiler pass.

5.1.2: Force make services public by default.

5.1.9: Remove constructor from abstract configuration class to make it more easy extendable.

6.0.1: Add "Valid" validation constraint to arrays of objects.

6.0.2: Allow to return null in ParameterValueConverterInterface::toString().

6.0.3: Detect data class in configuration form type.

6.0.4:
 
- Do not store null values.

- Reset values to default on save.

6.1.0: Add ConfigurationInterface::getOptions().

6.1.2: Move service config to "services" dir.

6.1.3: Register interfaces for autoconfiguration.
