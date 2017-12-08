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