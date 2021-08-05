# Openapi Data Mocker

[![Latest Stable Version](https://poser.pugx.org/ybelenko/openapi-data-mocker/v/stable)](https://packagist.org/packages/ybelenko/openapi-data-mocker)
[![Build Status](https://travis-ci.com/ybelenko/openapi-data-mocker.svg?branch=master)](https://travis-ci.com/ybelenko/openapi-data-mocker)
[![Coverage Status](https://coveralls.io/repos/github/ybelenko/openapi-data-mocker/badge.svg?branch=master)](https://coveralls.io/github/ybelenko/openapi-data-mocker?branch=master)
[![License](https://poser.pugx.org/ybelenko/openapi-data-mocker/license)](https://packagist.org/packages/ybelenko/openapi-data-mocker)

Openapi Data Mocker helps to generate fake data from OpenAPI 3.0 documents. Most of the methods may work with 2.0 version(fka Swagger 2.0), but it's not tested. This package was an enhancement of PHP Slim4 server in [OpenAPI Generator](https://github.com/OpenAPITools/openapi-generator) project, but it easier to maintain it in separated repo.

## Requirements
- PHP ^7.3

__Important notice! While PHP 8.0 declared in composer.json this package hasn't been tested against it.__

## Installation via Composer

Run in terminal:
```console
composer require ybelenko/openapi-data-mocker
```

## Usage example

Imagine we have [OpenAPI Specification 3.0.3 - Schema Object](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.3.md#schema-object) like this:
```yaml
description: Real world example schema
type: object
properties:
  id:
    type: integer
    format: int32
    minimum: 1
  purchased_items:
    type: array
    items:
      type: object
      properties:
        SKU:
          type: string
          format: uuid
          maxLength: 20
        quantity:
          type: integer
          format: int32
          minimum: 1
          maximum: 5
        price:
          type: object
          properties:
            currency:
              type: string
              minLength: 3
              maxLength: 3
              enum:
              - USD
              - EUR
              - RUB
            value:
              type: number
              format: float
              minimum: 0.01
              maximum: 99.99
        manufacturer:
          type: object
          properties:
            name:
              type: string
              maxLength: 30
            country:
              type: string
              enum:
              - CHN
              - USA
              - RUS
  buyer:
    type: object
    properties:
      first_name:
        type: string
        minLength: 3
        maxLength: 15
      last_name:
        type: string
        minLength: 3
        maxLength: 15
      credit_card:
        type: integer
        minimum: 1000000000000000
        maximum: 10000000000000000
      phone:
        type: integer
        minimum: 10000000000000
        maximum: 99999999999999
      email:
        type: string
        format: email
  status:
    type: string
    enum:
    - registered
    - paid
    - shipped
    - delivered
    default: registered
  created_at:
    type: string
    format: date-time
```
> Notice! While schema object presented in YAML format this library doesn't support YAML or JSON parsing right now. It means that `mockSchemaObject` method expects already decoded JSON value as argument.

When we mock mentioned schema with `mockSchemaObject` method:
```php
require __DIR__ . '/vendor/autoload.php';

use OpenAPIServer\Mock\OpenApiDataMocker as Mocker;
$mocker = new Mocker();
// set model classes namespace for $ref handling
// current example doesn't use $refs in schemas, however
$mocker->setModelsNamespace('JohnDoesPackage\\Model\\');
// class InvoiceTest contains schema mentioned previously
// it returns that schema with getOpenApiSchema() method declared in OpenAPIServer\Mock\BaseModel parent class
$schema = \OpenAPIServer\Mock\Model\InvoiceTest::getOpenApiSchema();
$data = $mocker->mockSchemaObject($schema);
echo json_encode($data, \JSON_PRETTY_PRINT);
```

the output looks like:
```json
{
    "id": 1912777939,
    "purchased_items": [
        {
            "SKU": "5ee78cfde9f05",
            "quantity": 4,
            "price": {
                "currency": "EUR",
                "value": 57.635
            },
            "manufacturer": {
                "name": "Lorem i",
                "country": "USA"
            }
        }
    ],
    "buyer": {
        "first_name": "Lorem ipsum do",
        "last_name": "Lorem ipsum ",
        "credit_card": 2455087473915908,
        "phone": 65526260517693,
        "email": "jfkennedy@example.com"
    },
    "status": "delivered",
    "created_at": "1978-08-08T04:03:09+00:00"
}
```
Of course that output will be slightly different on every call. That's what mocker package has been developed for.

You can check extended example at [examples/extended_example.php](examples/extended_example.php).

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
