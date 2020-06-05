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

use OpenAPIServer\Utils\StringUtilsTrait as StringUtils;
use PHPUnit\Framework\TestCase;

/**
 * StringUtilsTraitTest
 *
 * @coversDefaultClass \OpenAPIServer\Utils\StringUtilsTrait
 */
class StringUtilsTraitTest extends TestCase
{
    /**
     * @covers ::camelize
     * @dataProvider provideWordsForCamelizeTest
     */
    public function testCamelize($word, $lowercaseFirstLetter, $expectedWord)
    {
        $this->assertSame($expectedWord, StringUtils::camelize($word, $lowercaseFirstLetter));
    }

    public function provideWordsForCamelizeTest()
    {
        return [
            // fixtures from modules/openapi-generator/src/test/java/org/openapitools/codegen/utils/StringUtilsTest.java
            ['openApiServer/model/pet', null, 'OpenApiServerModelPet'],
            ['abcd', null, 'Abcd'],
            ['some-value', null, 'SomeValue'],
            ['some-Value', null, 'SomeValue'],
            ['some_value', null, 'SomeValue'],
            ['some_Value', null, 'SomeValue'],
            ['$type', null, '$Type'],

            ['abcd', true, 'abcd'],
            ['some-value', true, 'someValue'],
            ['some_value', true, 'someValue'],
            ['Abcd', true, 'abcd'],
            ['$type', true, '$type'],

            ['123', true, '123'],
            ['$123', true, '$123'],
            ['_foobar_Objects', null, 'FoobarObjects'],
            ['_foobar_Objects_small_Big', null, 'FoobarObjectsSmallBig'],
            ['inline_response_200', null, 'InlineResponse200'],
        ];
    }

    /**
     * @covers ::isReservedWord
     * @dataProvider provideWordsForIsReservedTest
     */
    public function testisReservedWord($word, $expected)
    {
        $this->assertSame($expected, StringUtils::isReservedWord($word));
    }

    public function provideWordsForIsReservedTest()
    {
        return [
            ['return', true],
            ['switch', true],
            ['class', true],
            ['interface', true],
            ['ABSTRACT', true],
            ['Trait', true],
            ['final', true],
            ['foobar', false],
            ['DateTime', false],
            ['Pet', false],
            [123, false],
            [null, false],
        ];
    }
}
