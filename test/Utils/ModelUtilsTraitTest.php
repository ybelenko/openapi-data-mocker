<?php

/**
 * Openapi Data Mocker
 * PHP version 7.2
 *
 * @package OpenAPIServer\Mock
 * @link    https://github.com/ybelenko/openapi-data-mocker
 * @author  Yuriy Belenko <yura-bely@mail.ru>
 * @license MIT
 */

namespace OpenAPIServer\Utils;

use OpenAPIServer\Utils\ModelUtilsTrait as ModelUtils;
use PHPUnit\Framework\TestCase;

/**
 * ModelUtilsTraitTest
 *
 * @coversDefaultClass \OpenAPIServer\Utils\ModelUtilsTrait
 */
class ModelUtilsTraitTest extends TestCase
{
    /**
     * @covers ::getSimpleRef
     * @dataProvider provideRefs
     */
    public function testGetSimpleRef($ref, $expectedRef)
    {
        $this->assertSame($expectedRef, ModelUtils::getSimpleRef($ref));
    }

    public function provideRefs()
    {
        return [
            'Reference Object OAS 3.0' => [
                '#/components/schemas/Pet', 'Pet',
            ],
            'Reference Object Swagger 2.0' => [
                '#/definitions/Pet', 'Pet',
            ],
            'Underscored classname' => [
                '#/components/schemas/_foobar_Objects', '_foobar_Objects',
            ],
            'Relative Documents With Embedded Schema' => [
                'definitions.json#/Pet', null,
            ],
            'null as argument' => [
                null, null,
            ],
            'number as argument' => [
                156, null,
            ],
            'inline response 200' => [
                '#/components/schemas/inline_response_200', 'inline_response_200',
            ],
        ];
    }

    /**
     * @covers ::toModelName
     * @dataProvider provideModelNames
     */
    public function testToModelName($name, $prefix, $suffix, $expectedModel)
    {
        $this->assertSame($expectedModel, ModelUtils::toModelName($name, $prefix, $suffix));
    }

    public function provideModelNames()
    {
        return [
            // fixtures from modules/openapi-generator/src/test/java/org/openapitools/codegen/utils/StringUtilsTest.java
            ['abcd', null, null, 'Abcd'],
            ['some-value', null, null, 'SomeValue'],
            ['some_value', null, null, 'SomeValue'],
            ['$type', null, null, 'Type'],
            ['123', null, null, 'Model123'],
            ['$123', null, null, 'Model123'],
            ['return', null, null, 'ModelReturn'],
            ['200Response', null, null, 'Model200Response'],
            ['abcd', 'SuperModel', null, 'SuperModelAbcd'],
            ['abcd', null, 'WithEnd', 'AbcdWithEnd'],
            ['abcd', 'WithStart', 'AndEnd', 'WithStartAbcdAndEnd'],
            ['_foobar_Objects', null, null, 'FoobarObjects'],
            [null, null, null, null],
            ['inline_response_200', null, null, 'InlineResponse200'],
        ];
    }
}
