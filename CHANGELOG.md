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
