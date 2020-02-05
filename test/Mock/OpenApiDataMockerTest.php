<?php

/**
 * OpenApiDataMockerTest
 *
 * PHP version 7.1
 *
 * @package OpenAPIServer
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */

/**
 * OpenAPI Petstore
 *
 * This spec is mainly for testing Petstore server and contains fake endpoints, models. Please do not use this for any other purpose. Special characters: \" \\
 * The version of the OpenAPI document: 1.0.0
 * Generated by: https://github.com/openapitools/openapi-generator.git
 */

/**
 * NOTE: This class is auto generated by the openapi generator program.
 * https://github.com/openapitools/openapi-generator
 * Do not edit the class manually.
 */
namespace OpenAPIServer\Mock;

use OpenAPIServer\Mock\OpenApiDataMocker;
use OpenAPIServer\Mock\OpenApiDataMockerInterface as IMocker;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsType;

/**
 * OpenApiDataMockerTest Class Doc Comment
 *
 * @package OpenAPIServer\Mock
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 * @coversDefaultClass \OpenAPIServer\Mock\OpenApiDataMocker
 */
class OpenApiDataMockerTest extends TestCase
{
    /**
     * @covers ::mock
     * @dataProvider provideMockCorrectArguments
     */
    public function testMockCorrectArguments($dataType, $dataFormat, $options, $expectedType)
    {
        $mocker = new OpenApiDataMocker();
        $this->assertInternalType($expectedType, $mocker->mock($dataType));
    }

    public function provideMockCorrectArguments()
    {
        return [
            [IMocker::DATA_TYPE_INTEGER, null, null, IsType::TYPE_INT],
            [IMocker::DATA_TYPE_NUMBER, null, null, IsType::TYPE_FLOAT],
            [IMocker::DATA_TYPE_STRING, null, null, IsType::TYPE_STRING],
            [IMocker::DATA_TYPE_BOOLEAN, null, null, IsType::TYPE_BOOL],
        ];
    }

    /**
     * @dataProvider provideMockIntegerCorrectArguments
     * @covers ::mockInteger
     */
    public function testMockIntegerWithCorrectArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false,
        $matchingInternalTypes = [],
        $notMatchingInternalTypes = []
    ) {
        $mocker = new OpenApiDataMocker();
        $integer = $mocker->mockInteger($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);

        $this->internalAssertNumber(
            $integer,
            $minimum,
            $maximum,
            $exclusiveMinimum,
            $exclusiveMaximum,
            $matchingInternalTypes,
            $notMatchingInternalTypes
        );
    }

    public function provideMockIntegerCorrectArguments()
    {
        $types = [
            IsType::TYPE_INT,
            IsType::TYPE_NUMERIC,
            IsType::TYPE_SCALAR,
        ];
        $notTypes = [
            IsType::TYPE_ARRAY,
            IsType::TYPE_BOOL,
            IsType::TYPE_FLOAT,
            IsType::TYPE_NULL,
            IsType::TYPE_OBJECT,
            IsType::TYPE_RESOURCE,
            IsType::TYPE_STRING,
            IsType::TYPE_CALLABLE,
        ];

        return [
            [null, -100, 100, false, false, $types, $notTypes],
            [null, -100, null, false, false, $types, $notTypes],
            [null, null, 100, false, false, $types, $notTypes],
            [null, -99.5, null, true, false, $types, $notTypes],
            [null, null, 99.5, false, true, $types, $notTypes],
            [null, -99.5, 99.5, true, true, $types, $notTypes],
        ];
    }

    /**
     * @dataProvider provideMockIntegerInvalidArguments
     * @covers ::mockInteger
     * @expectedException \InvalidArgumentException
     */
    public function testMockIntegerWithInvalidArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        $mocker = new OpenApiDataMocker();
        $integer = $mocker->mockInteger($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
    }

    public function provideMockIntegerInvalidArguments()
    {
        return [
            [null, 'foo', null, false, false],
            [null, null, false, false, false],
            [null, null, null, true, false],
            [null, null, null, false, true],
            [null, 100, -100, false, false],
        ];
    }

    /**
     * @dataProvider provideMockNumberCorrectArguments
     * @covers ::mockNumber
     */
    public function testMockNumberWithCorrectArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false,
        $matchingInternalTypes = [],
        $notMatchingInternalTypes = []
    ) {
        $mocker = new OpenApiDataMocker();
        $number = $mocker->mockNumber($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);

        $this->internalAssertNumber(
            $number,
            $minimum,
            $maximum,
            $exclusiveMinimum,
            $exclusiveMaximum,
            $matchingInternalTypes,
            $notMatchingInternalTypes
        );
    }

    public function provideMockNumberCorrectArguments()
    {
        $types = [
            IsType::TYPE_SCALAR,
            IsType::TYPE_NUMERIC,
            IsType::TYPE_FLOAT,
        ];
        $notTypes = [
            IsType::TYPE_INT,
            IsType::TYPE_ARRAY,
            IsType::TYPE_BOOL,
            IsType::TYPE_NULL,
            IsType::TYPE_OBJECT,
            IsType::TYPE_RESOURCE,
            IsType::TYPE_STRING,
            IsType::TYPE_CALLABLE,
        ];

        return [
            [null, -100, 100, false, false, $types, $notTypes],
            [null, -100, null, false, false, $types, $notTypes],
            [null, null, 100, false, false, $types, $notTypes],
            [null, -99.5, null, true, false, $types, $notTypes],
            [null, null, 99.5, false, true, $types, $notTypes],
            [null, -99.5, 99.5, true, true, $types, $notTypes],
        ];
    }

    /**
     * @dataProvider provideMockNumberInvalidArguments
     * @expectedException \InvalidArgumentException
     * @covers ::mockNumber
     */
    public function testMockNumberWithInvalidArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        $mocker = new OpenApiDataMocker();
        $number = $mocker->mockNumber($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
    }

    public function provideMockNumberInvalidArguments()
    {
        return [
            [null, 'foo', null, false, false],
            [null, null, false, false, false],
            [null, null, null, true, false],
            [null, null, null, false, true],
            [null, 100, -100, false, false],
        ];
    }

    /**
     * @dataProvider provideMockStringCorrectArguments
     * @covers ::mockString
     */
    public function testMockStringWithCorrectArguments(
        $dataFormat = null,
        $minLength = 0,
        $maxLength = null,
        $matchingInternalTypes = [],
        $notMatchingInternalTypes = []
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength);

        $this->internalAssertString(
            $str,
            $minLength,
            $maxLength,
            $matchingInternalTypes,
            $notMatchingInternalTypes
        );
    }

    public function provideMockStringCorrectArguments()
    {
        $types = [
            IsType::TYPE_SCALAR,
            IsType::TYPE_STRING,
        ];
        $notTypes = [
            IsType::TYPE_NUMERIC,
            IsType::TYPE_FLOAT,
            IsType::TYPE_INT,
            IsType::TYPE_ARRAY,
            IsType::TYPE_BOOL,
            IsType::TYPE_NULL,
            IsType::TYPE_OBJECT,
            IsType::TYPE_RESOURCE,
            IsType::TYPE_CALLABLE,
        ];

        return [
            [null, 0, null, $types, $notTypes],
            [null, 10, null, $types, $notTypes],
            [null, 0, 100, $types, $notTypes],
            [null, 10, 50, $types, $notTypes],
            [null, 10, 10, $types, $notTypes],
            [null, 0, 0, $types, $notTypes],
            [null, null, null, $types, $notTypes],
        ];
    }

    /**
     * @dataProvider provideMockStringInvalidArguments
     * @expectedException \InvalidArgumentException
     * @covers ::mockString
     */
    public function testMockStringWithInvalidArguments(
        $dataFormat = null,
        $minLength = 0,
        $maxLength = null
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength);
    }

    public function provideMockStringInvalidArguments()
    {
        return [
            [null, -10, null],
            [null, 0, -10],
            [null, -10, -10],
            [null, 0.5, 0.5],
            [null, '10', null],
            [null, 0, '50'],
            [null, '10', '50'],
            [null, 50, 10],
        ];
    }

    /**
     * @covers ::mockBoolean
     */
    public function testMockBoolean()
    {
        $mocker = new OpenApiDataMocker();
        $bool = $mocker->mockBoolean();

        $matchingInternalTypes = [
            IsType::TYPE_SCALAR,
            IsType::TYPE_BOOL,
        ];

        foreach ($matchingInternalTypes as $matchType) {
            $this->assertInternalType($matchType, $bool);
        }

        $notMatchingInternalTypes = [
            IsType::TYPE_NUMERIC,
            IsType::TYPE_FLOAT,
            IsType::TYPE_INT,
            IsType::TYPE_ARRAY,
            IsType::TYPE_STRING,
            IsType::TYPE_NULL,
            IsType::TYPE_OBJECT,
            IsType::TYPE_RESOURCE,
            IsType::TYPE_CALLABLE,
        ];

        foreach ($notMatchingInternalTypes as $notMatchType) {
            $this->assertNotInternalType($notMatchType, $bool);
        }
    }

    private function internalAssertNumber(
        $number,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false,
        $matchingInternalTypes = [],
        $notMatchingInternalTypes = []
    ) {
        foreach ($matchingInternalTypes as $matchType) {
            $this->assertInternalType($matchType, $number);
        }

        foreach ($notMatchingInternalTypes as $notMatchType) {
            $this->assertNotInternalType($notMatchType, $number);
        }

        if ($minimum !== null) {
            if ($exclusiveMinimum) {
                $this->assertGreaterThan($minimum, $number);
            } else {
                $this->assertGreaterThanOrEqual($minimum, $number);
            }
        }

        if ($maximum !== null) {
            if ($exclusiveMaximum) {
                $this->assertLessThan($maximum, $number);
            } else {
                $this->assertLessThanOrEqual($maximum, $number);
            }
        }
    }

    private function internalAssertString(
        $str,
        $minLength = null,
        $maxLength = null,
        $matchingInternalTypes = [],
        $notMatchingInternalTypes = []
    ) {
        foreach ($matchingInternalTypes as $matchType) {
            $this->assertInternalType($matchType, $str);
        }

        foreach ($notMatchingInternalTypes as $notMatchType) {
            $this->assertNotInternalType($notMatchType, $str);
        }

        if ($minLength !== null) {
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str, 'UTF-8'));
        }

        if ($maxLength !== null) {
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str, 'UTF-8'));
        }
    }
}
