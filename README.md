# Openapi Data Mocker

[![Latest Stable Version](https://poser.pugx.org/ybelenko/openapi-data-mocker/v/stable)](https://packagist.org/packages/ybelenko/openapi-data-mocker)
[![Build Status](https://travis-ci.com/ybelenko/openapi-data-mocker.svg?branch=master)](https://travis-ci.com/ybelenko/openapi-data-mocker)
[![Coverage Status](https://coveralls.io/repos/github/ybelenko/openapi-data-mocker/badge.svg?branch=master)](https://coveralls.io/github/ybelenko/openapi-data-mocker?branch=master)
[![License](https://poser.pugx.org/ybelenko/openapi-data-mocker/license)](https://packagist.org/packages/ybelenko/openapi-data-mocker)

Openapi Data Mocker helps to generate fake data from OpenAPI 3.0 documents. Most of the methods may work with 2.0 version(fka Swagger 2.0), but it's not tested. This package was an enhancement of PHP Slim4 server in [OpenAPI Generator](https://github.com/OpenAPITools/openapi-generator) project, but it easier to maintain it in separated repo.

## Requirements
- PHP ^7.2

## Installation via Composer

Run in terminal:
```console
composer require ybelenko/openapi-data-mocker
```

## Usage example

```php
require __DIR__ . '/vendor/autoload.php';

use OpenAPIServer\Mock\OpenApiDataMocker as Mocker;
$mocker = new Mocker();
// set model classes namespace for $ref handling
$mocker->setModelsNamespace('JohnDoesPackage\\Model\\');
$data = [
    'Integer from 1 to 100' => $mocker->mockInteger(null, 1, 100),
    'Float from -3 to 3' =>  $mocker->mockNumber(null, -3, 3),
    'String 10 chars' => $mocker->mockString(null, 10, 10),
    'Boolean' =>  $mocker->mockBoolean(),
    'Array of strings' => $mocker->mockArray(
        [
            'type' => 'string',
            'maxLength' => 20,
        ]
    ),
    'Object' => $mocker->mockObject([
        'id' => [
            'type' => 'integer',
            'minimum' => 1,
            'maximum' => 10
        ],
        'username' => [
            'type' => 'string',
            'maxLength' => 10,
        ]
    ])
];

echo json_encode($data, JSON_PRETTY_PRINT);
```

## Supported features

All data types supported except specific string formats: `email`, `uuid`, `password` which are poorly implemented.

### Data Types Support

| Data Type | Data Format |      Supported     |
|:---------:|:-----------:|:------------------:|
| `integer` | `int32`     | :white_check_mark: |
| `integer` | `int64`     | :white_check_mark: |
| `number`  | `float`     | :white_check_mark: |
| `number`  | `double`    |                    |
| `string`  | `byte`      | :white_check_mark: |
| `string`  | `binary`    | :white_check_mark: |
| `boolean` |             | :white_check_mark: |
| `string`  | `date`      | :white_check_mark: |
| `string`  | `date-time` | :white_check_mark: |
| `string`  | `password`  | :white_check_mark: |
| `string`  | `email`     | :white_check_mark: |
| `string`  | `uuid`      | :white_check_mark: |

### Data Options Support

| Data Type   |         Option         |      Supported     |
|:-----------:|:----------------------:|:------------------:|
| `string`    | `minLength`            | :white_check_mark: |
| `string`    | `maxLength`            | :white_check_mark: |
| `string`    | `enum`                 | :white_check_mark: |
| `string`    | `pattern`              |                    |
| `integer`   | `minimum`              | :white_check_mark: |
| `integer`   | `maximum`              | :white_check_mark: |
| `integer`   | `exclusiveMinimum`     | :white_check_mark: |
| `integer`   | `exclusiveMaximum`     | :white_check_mark: |
| `number`    | `minimum`              | :white_check_mark: |
| `number`    | `maximum`              | :white_check_mark: |
| `number`    | `exclusiveMinimum`     | :white_check_mark: |
| `number`    | `exclusiveMaximum`     | :white_check_mark: |
| `array`     | `items`                | :white_check_mark: |
| `array`     | `additionalItems`      |                    |
| `array`     | `minItems`             | :white_check_mark: |
| `array`     | `maxItems`             | :white_check_mark: |
| `array`     | `uniqueItems`          |                    |
| `object`    | `properties`           | :white_check_mark: |
| `object`    | `maxProperties`        |                    |
| `object`    | `minProperties`        |                    |
| `object`    | `patternProperties`    |                    |
| `object`    | `additionalProperties` |                    |
| `object`    | `required`             |                    |
| `*`         | `$ref`                 | :white_check_mark: |
| `*`         | `allOf`                |                    |
| `*`         | `anyOf`                |                    |
| `*`         | `oneOf`                |                    |
| `*`         | `not`                  |                    |

## Known Limitations

Avoid circular refs in your schema. Schema below can cause infinite loop and `Out of Memory` PHP error:
```yml
# ModelA has reference to ModelB while ModelB has reference to ModelA.
# Mock server will produce huge nested JSON example and ended with `Out of Memory` error.
definitions:
  ModelA:
    type: object
    properties:
      model_b:
        $ref: '#/definitions/ModelB'
  ModelB:
    type: array
    items:
      $ref: '#/definitions/ModelA'
```

Don't ref scalar types, because generator will not produce models which mock server can find. So schema below will cause error:
```yml
# generated build contains only `OuterComposite` model class which referenced to not existed `OuterNumber`, `OuterString`, `OuterBoolean` classes
# mock server cannot mock `OuterComposite` model and throws exception
definitions:
  OuterComposite:
    type: object
    properties:
      my_number:
        $ref: '#/definitions/OuterNumber'
      my_string:
        $ref: '#/definitions/OuterString'
      my_boolean:
        $ref: '#/definitions/OuterBoolean'
  OuterNumber:
    type: number
  OuterString:
    type: string
  OuterBoolean:
    type: boolean
```

## Links to mentioned technologies

* [OpenAPI Specification 3.0.3](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.3.md)
* [OpenAPI Generator](https://openapi-generator.tech)
* [Composer](https://getcomposer.org/download/)
