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

namespace OpenAPIServer\Mock;

use OpenAPIServer\Mock\OpenApiDataMocker;
use OpenAPIServer\Mock\OpenApiDataMockerInterface as IMocker;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsType;
use StdClass;
use DateTime;
use InvalidArgumentException;

/**
 * OpenApiDataMockerTest
 *
 * phpcs:disable Squiz.Commenting,Generic.Commenting,PEAR.Commenting
 * @coversDefaultClass \OpenAPIServer\Mock\OpenApiDataMocker
 */
class OpenApiDataMockerTest extends TestCase
{
    /**
     * @covers ::mock
     * @dataProvider provideMockCorrectArguments
     */
    public function testMockCorrectArguments($dataType, $dataFormat, $options, $assertMethod)
    {
        $mocker = new OpenApiDataMocker();
        $data = $mocker->mock($dataType, $dataFormat, $options);
        $this->$assertMethod($data);
    }

    public function provideMockCorrectArguments()
    {
        return [
            [IMocker::DATA_TYPE_INTEGER, null, null, 'assertIsInt'],
            [IMocker::DATA_TYPE_NUMBER, null, null, 'assertIsFloat'],
            [IMocker::DATA_TYPE_STRING, null, null, 'assertIsString'],
            [IMocker::DATA_TYPE_BOOLEAN, null, null, 'assertIsBool'],
            [IMocker::DATA_TYPE_ARRAY, null, [
                'items' => [
                    'type' => IMocker::DATA_TYPE_INTEGER,
                ],
            ], 'assertIsArray'],
            [IMocker::DATA_TYPE_OBJECT, null, [
                'properties' => [
                    'username' => [
                        'type' => IMocker::DATA_TYPE_INTEGER,
                    ],
                ],
            ], 'assertIsObject'],
        ];
    }

    /**
     * @covers ::mock
     * @dataProvider provideMockInvalidArguments
     */
    public function testMockInvalidArguments($dataType, $dataFormat, $options)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"dataType" must be one of integer, number, string, boolean, array, object');
        $mocker = new OpenApiDataMocker();
        $data = $mocker->mock($dataType, $dataFormat, $options);
    }

    public function provideMockInvalidArguments()
    {
        return [
            ['foobar', null, null],
            [3.14, null, null],
        ];
    }

    /**
     * @covers ::mock
     */
    public function testMockWithStringEnumOptions()
    {
        $mocker = new OpenApiDataMocker();
        $string = $mocker->mock(IMocker::DATA_TYPE_STRING, null, [
            'enum' => ['foobar', 'foobaz', 'helloworld'],
        ]);
        $this->assertContains($string, ['foobar', 'foobaz', 'helloworld']);
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
        $typeAssertionMethods = []
    ) {
        $mocker = new OpenApiDataMocker();
        $integer = $mocker->mockInteger($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);

        $this->internalAssertNumber(
            $integer,
            $minimum,
            $maximum,
            $exclusiveMinimum,
            $exclusiveMaximum,
            $typeAssertionMethods
        );
    }

    public function provideMockIntegerCorrectArguments()
    {
        $typeAssertionMethods = [
            'assertIsInt',
            'assertIsNumeric',
            'assertIsScalar',
            'assertIsNotArray',
            'assertIsNotBool',
            'assertIsNotFloat',
            'assertNotNull',
            'assertIsNotObject',
            'assertIsNotResource',
            'assertIsNotString',
            'assertIsNotCallable',
        ];

        return [
            [null, -100, 100, false, false, $typeAssertionMethods],
            [null, -100, null, false, false, $typeAssertionMethods],
            [null, null, 100, false, false, $typeAssertionMethods],
            [null, -99.5, null, true, false, $typeAssertionMethods],
            [null, null, 99.5, false, true, $typeAssertionMethods],
            [null, -99.5, 99.5, true, true, $typeAssertionMethods],
        ];
    }

    /**
     * @dataProvider provideMockIntegerInvalidArguments
     * @covers ::mockInteger
     */
    public function testMockIntegerWithInvalidArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $integer = $mocker->mockInteger($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
    }

    public function provideMockIntegerInvalidArguments()
    {
        return [
            [null, null, null, true, false],
            [null, null, null, false, true],
            [null, 100, -100, false, false],
        ];
    }

    /**
     * @covers ::mockInteger
     * @dataProvider provideMockIntegerFormats
     */
    public function testMockIntegerWithFormats(
        $dataFormat,
        $minimum,
        $maximum,
        $expectedMin,
        $expectedMax
    ) {
        $mocker = new OpenApiDataMocker();
        $integer = $mocker->mockInteger($dataFormat, $minimum, $maximum);
        $this->assertGreaterThanOrEqual($expectedMin, $integer);
        $this->assertLessThanOrEqual($expectedMax, $integer);
    }

    public function provideMockIntegerFormats()
    {
        return [
            [IMocker::DATA_FORMAT_INT32, -2147483648, 2147483648, -2147483647, 2147483647],
            [IMocker::DATA_FORMAT_INT64, '-9223372036854775808', '9223372036854775808', -9223372036854775807, 9223372036854775807],
            [IMocker::DATA_FORMAT_INT32, -10, 10, -10, 10],
            [IMocker::DATA_FORMAT_INT64, -10, 10, -10, 10],
            [IMocker::DATA_FORMAT_INT32, -9223372036854775807, 9223372036854775807, -2147483647, 2147483647],
            [strtoupper(IMocker::DATA_FORMAT_INT32), -2147483648, 2147483648, -2147483647, 2147483647],
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
        $typeAssertionMethods = []
    ) {
        $mocker = new OpenApiDataMocker();
        $number = $mocker->mockNumber($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);

        $this->internalAssertNumber(
            $number,
            $minimum,
            $maximum,
            $exclusiveMinimum,
            $exclusiveMaximum,
            $typeAssertionMethods
        );
    }

    public function provideMockNumberCorrectArguments()
    {
        $typeAssertionMethods = [
            'assertIsScalar',
            'assertIsNumeric',
            'assertIsFloat',
            'assertIsNotInt',
            'assertIsNotArray',
            'assertIsNotBool',
            'assertNotNull',
            'assertIsNotObject',
            'assertIsNotResource',
            'assertIsNotString',
            'assertIsNotCallable',
        ];

        return [
            [null, -100, 100, false, false, $typeAssertionMethods],
            [null, -100, null, false, false, $typeAssertionMethods],
            [null, null, 100, false, false, $typeAssertionMethods],
            [null, -99.5, null, true, false, $typeAssertionMethods],
            [null, null, 99.5, false, true, $typeAssertionMethods],
            [null, -99.5, 99.5, true, true, $typeAssertionMethods],
        ];
    }

    /**
     * @dataProvider provideMockNumberInvalidArguments
     * @covers ::mockNumber
     */
    public function testMockNumberWithInvalidArguments(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $number = $mocker->mockNumber($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
    }

    public function provideMockNumberInvalidArguments()
    {
        return [
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
        $enum = null,
        $typeAssertionMethods = []
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength, $enum);

        $this->internalAssertString(
            $str,
            $minLength,
            $maxLength,
            $enum,
            $typeAssertionMethods
        );
    }

    public function provideMockStringCorrectArguments()
    {
        $typeAssertionMethods = [
            'assertIsScalar',
            'assertIsString',
            'assertIsNotFloat',
            'assertIsNotInt',
            'assertIsNotArray',
            'assertIsNotBool',
            'assertNotNull',
            'assertIsNotObject',
            'assertIsNotResource',
            'assertIsNotCallable',
        ];

        return [
            [null, 0, null, null, $typeAssertionMethods],
            [null, 10, null, null, $typeAssertionMethods],
            [null, 0, 100, null, $typeAssertionMethods],
            [null, 10, 50, null, $typeAssertionMethods],
            [null, 10, 10, null, $typeAssertionMethods],
            [null, 0, 0, null, $typeAssertionMethods],
            [null, null, null, null, $typeAssertionMethods],
            [null, null, null, ['foobar', 'foobaz', 'hello world'], $typeAssertionMethods],
            [null, null, null, ['foobar'], $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 0, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 10, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 0, 100, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 10, 50, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 10, 10, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_PASSWORD, 0, 0, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, null, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 10, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 10, 10, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, null, 8, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 16, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 25, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 25, 25, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, null, 20, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 30, null, null, $typeAssertionMethods],
            [IMocker::DATA_FORMAT_EMAIL, 1, 1, null, $typeAssertionMethods],
        ];
    }

    /**
     * @dataProvider provideMockStringInvalidArguments
     * @covers ::mockString
     */
    public function testMockStringWithInvalidArguments(
        $dataFormat = null,
        $minLength = 0,
        $maxLength = null,
        $enum = null
    ) {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength, $enum);
    }

    public function provideMockStringInvalidArguments()
    {
        return [
            'negative minLength' => [null, -10, null],
            'negative maxLength' => [null, 0, -10],
            'both minLength maxLength negative' => [null, -10, -10],
            'maxLength less than minLength' => [null, 50, 10],
            'enum is empty array' => [null, null, null, []],
            'enum array is not unique' => [null, null, null, ['foobar', 'foobaz', 'foobar']],
        ];
    }

    /**
     * @covers ::mock
     * @covers ::mockString
     * @dataProvider provideMockStringByteFormatArguments
     */
    public function testMockStringWithByteFormat(
        $dataFormat,
        $minLength,
        $maxLength
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength);
        $str2 = $mocker->mock(IMocker::DATA_TYPE_STRING, $dataFormat, ['minLength' => $minLength, 'maxLength' => $maxLength]);
        $base64pattern = '/^[\w\+\/\=]*$/';
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression($base64pattern, $str);
            $this->assertMatchesRegularExpression($base64pattern, $str2);
        } elseif (method_exists($this, 'assertRegExp')) {
            $this->assertRegExp($base64pattern, $str);
            $this->assertRegExp($base64pattern, $str2);
        } else {
            $this->markTestIncomplete('No method to assert RegExp');
        }

        if ($minLength !== null) {
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str));
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str2));
        }
        if ($maxLength !== null) {
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str));
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str2));
        }
    }

    public function provideMockStringByteFormatArguments()
    {
        return [
            [IMocker::DATA_FORMAT_BYTE, null, null],
            [IMocker::DATA_FORMAT_BYTE, 10, null],
            [IMocker::DATA_FORMAT_BYTE, 10, 10],
            [IMocker::DATA_FORMAT_BYTE, null, 12],
        ];
    }

    /**
     * @covers ::mock
     * @covers ::mockString
     * @dataProvider provideMockStringDateFormatArguments
     */
    public function testMockStringWithDateAndDateTimeFormat(
        $dataFormat,
        $minLength,
        $maxLength,
        $dtFormat
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength);
        $str2 = $mocker->mock(IMocker::DATA_TYPE_STRING, $dataFormat, ['minLength' => $minLength, 'maxLength' => $maxLength]);

        if ($dtFormat !== null) {
            $date = DateTime::createFromFormat($dtFormat, $str);
            $date2 = DateTime::createFromFormat($dtFormat, $str2);
            $this->assertInstanceOf(DateTime::class, $date);
            $this->assertInstanceOf(DateTime::class, $date2);
        }
        if ($minLength !== null) {
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str));
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str2));
        }
        if ($maxLength !== null) {
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str));
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str2));
        }
    }

    public function provideMockStringDateFormatArguments()
    {
        return [
            [IMocker::DATA_FORMAT_DATE, null, null, 'Y-m-d'],
            [IMocker::DATA_FORMAT_DATE, 10, null, 'Y-m-d'],
            [IMocker::DATA_FORMAT_DATE, 10, 10, 'Y-m-d'],
            [IMocker::DATA_FORMAT_DATE, null, 8, null],
            [IMocker::DATA_FORMAT_DATE, 16, null, null],
            [IMocker::DATA_FORMAT_DATE_TIME, null, null, 'Y-m-d\TH:i:sP'],
            [IMocker::DATA_FORMAT_DATE_TIME, 25, null, 'Y-m-d\TH:i:sP'],
            [IMocker::DATA_FORMAT_DATE_TIME, 25, 25, 'Y-m-d\TH:i:sP'],
            [IMocker::DATA_FORMAT_DATE_TIME, null, 20, null],
            [IMocker::DATA_FORMAT_DATE_TIME, 30, null, null],
        ];
    }

    /**
     * @covers ::mock
     * @covers ::mockString
     * @dataProvider provideMockStringBinaryFormatArguments
     */
    public function testMockStringWithBinaryFormat(
        $dataFormat,
        $minLength,
        $maxLength
    ) {
        $mocker = new OpenApiDataMocker();
        $str = $mocker->mockString($dataFormat, $minLength, $maxLength);
        $str2 = $mocker->mock(IMocker::DATA_TYPE_STRING, $dataFormat, ['minLength' => $minLength, 'maxLength' => $maxLength]);
        if ($minLength !== null) {
            $this->assertGreaterThanOrEqual($minLength, strlen($str));
            $this->assertGreaterThanOrEqual($minLength, strlen($str2));
        }
        if ($maxLength !== null) {
            $this->assertLessThanOrEqual($maxLength, strlen($str));
            $this->assertLessThanOrEqual($maxLength, strlen($str2));
        }
    }

    public function provideMockStringBinaryFormatArguments()
    {
        return [
            [IMocker::DATA_FORMAT_BINARY, 0, null],
            [IMocker::DATA_FORMAT_BINARY, 10, null],
            [IMocker::DATA_FORMAT_BINARY, 0, 100],
            [IMocker::DATA_FORMAT_BINARY, 10, 50],
            [IMocker::DATA_FORMAT_BINARY, 10, 10],
            [IMocker::DATA_FORMAT_BINARY, 0, 0],
        ];
    }

    /**
     * @covers ::mock
     * @covers ::mockString
     * @dataProvider provideMockStringUuidFormatArguments
     */
    public function testMockStringWithUuidFormat(
        $minLength,
        $maxLength
    ) {
        $mocker = new OpenApiDataMocker();
        $arr = [];
        $arr2 = [];
        $hexPattern = '/^[a-f0-9]*$/';

        while (count($arr) < 100 && count($arr2) < 100) {
            $str = $mocker->mockString(IMocker::DATA_FORMAT_UUID, $minLength, $maxLength);
            $str2 = $mocker->mock(IMocker::DATA_TYPE_STRING, IMocker::DATA_FORMAT_UUID, ['minLength' => $minLength, 'maxLength' => $maxLength]);
            $arr[] = $str;
            $arr2[] = $str2;

            if (method_exists($this, 'assertMatchesRegularExpression')) {
                $this->assertMatchesRegularExpression($hexPattern, $str);
                $this->assertMatchesRegularExpression($hexPattern, $str2);
            } elseif (method_exists($this, 'assertRegExp')) {
                $this->assertRegExp($hexPattern, $str);
                $this->assertRegExp($hexPattern, $str2);
            } else {
                $this->markTestIncomplete('No method to assert RegExp');
            }

            if ($minLength !== null) {
                $this->assertGreaterThanOrEqual($minLength, mb_strlen($str));
                $this->assertGreaterThanOrEqual($minLength, mb_strlen($str2));
            }
            if ($maxLength !== null) {
                $this->assertLessThanOrEqual($maxLength, mb_strlen($str));
                $this->assertLessThanOrEqual($maxLength, mb_strlen($str2));
            }
        }
    }

    public function provideMockStringUuidFormatArguments()
    {
        return [
            [null, null],
            [10, null],
            [10, 10],
            [null, 8],
            [16, null],
            [null, null],
            [25, null],
            [25, 25],
            [null, 20],
            [30, null],
            [1, 1],
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
            'assertIsScalar',
            'assertIsBool',
            'assertIsNotNumeric',
            'assertIsNotFloat',
            'assertIsNotInt',
            'assertIsNotArray',
            'assertIsNotString',
            'assertNotNull',
            'assertIsNotObject',
            'assertIsNotResource',
            'assertIsNotCallable',
        ];

        foreach ($matchingInternalTypes as $assertMethod) {
            $this->$assertMethod($bool);
        }
    }

    private function internalAssertNumber(
        $number,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false,
        $typeAssertionMethods = []
    ) {
        foreach ($typeAssertionMethods as $assertMethod) {
            $this->$assertMethod($number);
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
        $enum = null,
        $typeAssertionMethods = []
    ) {
        foreach ($typeAssertionMethods as $assertMethod) {
            $this->$assertMethod($str);
        }

        if ($minLength !== null) {
            $this->assertGreaterThanOrEqual($minLength, mb_strlen($str, 'UTF-8'));
        }

        if ($maxLength !== null) {
            $this->assertLessThanOrEqual($maxLength, mb_strlen($str, 'UTF-8'));
        }

        if (is_array($enum) && !empty($enum)) {
            $this->assertContains($str, $enum);
        }
    }

    /**
     * @dataProvider provideMockArrayCorrectArguments
     * @covers ::mockArray
     */
    public function testMockArrayFlattenWithCorrectArguments(
        $items,
        $minItems,
        $maxItems,
        $uniqueItems,
        $expectedItemsType = null,
        $expectedArraySize = null
    ) {
        $mocker = new OpenApiDataMocker();
        $arr = $mocker->mockArray($items, $minItems, $maxItems, $uniqueItems);

        $this->assertIsArray($arr);
        if ($expectedArraySize !== null) {
            $this->assertCount($expectedArraySize, $arr);
        }
        if ($expectedItemsType && $expectedArraySize > 0) {
            $this->assertContainsOnly($expectedItemsType, $arr, true);
        }

        if (is_array($items)) {
            $dataType = $items['type'];
            $dataFormat = $items['dataFormat'] ?? null;

            // items field numeric properties
            $minimum = $items['minimum'] ?? null;
            $maximum = $items['maximum'] ?? null;
            $exclusiveMinimum = $items['exclusiveMinimum'] ?? null;
            $exclusiveMaximum = $items['exclusiveMaximum'] ?? null;

            // items field string properties
            $minLength = $items['minLength'] ?? null;
            $maxLength = $items['maxLength'] ?? null;
            $enum = $items['enum'] ?? null;
            $pattern = $items['pattern'] ?? null;

            // items field array properties
            $subItems = $items['items'] ?? null;
            $subMinItems = $items['minItems'] ?? null;
            $subMaxItems = $items['maxItems'] ?? null;
            $subUniqueItems = $items['uniqueItems'] ?? null;
        } else {
            // is object
            $dataType = $items->type;
            $dataFormat = $items->dataFormat ?? null;

            // items field numeric properties
            $minimum = $items->minimum ?? null;
            $maximum = $items->maximum ?? null;
            $exclusiveMinimum = $items->exclusiveMinimum ?? null;
            $exclusiveMaximum = $items->exclusiveMaximum ?? null;

            // items field string properties
            $minLength = $items->minLength ?? null;
            $maxLength = $items->maxLength ?? null;
            $enum = $items->enum ?? null;
            $pattern = $items->pattern ?? null;

            // items field array properties
            $subItems = $items->items ?? null;
            $subMinItems = $items->minItems ?? null;
            $subMaxItems = $items->maxItems ?? null;
            $subUniqueItems = $items->uniqueItems ?? null;
        }


        foreach ($arr as $item) {
            switch ($dataType) {
                case IMocker::DATA_TYPE_INTEGER:
                    $this->internalAssertNumber($item, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
                    break;
                case IMocker::DATA_TYPE_NUMBER:
                    $this->internalAssertNumber($item, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
                    break;
                case IMocker::DATA_TYPE_STRING:
                    $this->internalAssertString($item, $minLength, $maxLength);
                    break;
                case IMocker::DATA_TYPE_BOOLEAN:
                    $this->assertIsBool($item);
                    break;
                case IMocker::DATA_TYPE_ARRAY:
                    $this->testMockArrayFlattenWithCorrectArguments($subItems, $subMinItems, $subMaxItems, $subUniqueItems);
                    break;
            }
        }
    }

    public function provideMockArrayCorrectArguments()
    {
        $intItems = ['type' => IMocker::DATA_TYPE_INTEGER, 'minimum' => 5, 'maximum' => 10];
        $floatItems = ['type' => IMocker::DATA_TYPE_NUMBER, 'minimum' => -32.4, 'maximum' => 88.6, 'exclusiveMinimum' => true, 'exclusiveMaximum' => true];
        $strItems = ['type' => IMocker::DATA_TYPE_STRING, 'minLength' => 20, 'maxLength' => 50];
        $boolItems = ['type' => IMocker::DATA_TYPE_BOOLEAN];
        $arrayItems = ['type' => IMocker::DATA_TYPE_ARRAY, 'items' => ['type' => IMocker::DATA_TYPE_STRING, 'minItems' => 3, 'maxItems' => 10]];
        $objectItems = ['type' => IMocker::DATA_TYPE_OBJECT, 'properties' => ['username' => ['type' => IMocker::DATA_TYPE_STRING]]];
        $expectedInt = IsType::TYPE_INT;
        $expectedFloat = IsType::TYPE_FLOAT;
        $expectedStr = IsType::TYPE_STRING;
        $expectedBool = IsType::TYPE_BOOL;
        $expectedArray = IsType::TYPE_ARRAY;
        $expectedObject = IsType::TYPE_OBJECT;

        return [
            'empty array' => [
                $strItems, null, 0, false, null, 0,
            ],
            'empty array, limit zero' => [
                $strItems, 0, 0, false, null, 0,
            ],
            'array of one string as default size' => [
                $strItems, null, null, false, $expectedStr, 1,
            ],
            'array of one string, limit one' => [
                $strItems, 1, 1, false, $expectedStr, 1,
            ],
            'array of two strings' => [
                $strItems, 2, null, false, $expectedStr, 2,
            ],
            'array of five strings, limit ten' => [
                $strItems, 5, 10, false, $expectedStr, 5,
            ],
            'array of five strings, limit five' => [
                $strItems, 5, 5, false, $expectedStr, 5,
            ],
            'array of one string, limit five' => [
                $strItems, null, 5, false, $expectedStr, 1,
            ],
            'array of one integer' => [
                $intItems, null, null, false, $expectedInt, 1,
            ],
            'array of one float' => [
                $floatItems, null, null, false, $expectedFloat, 1,
            ],
            'array of one boolean' => [
                $boolItems, null, null, false, $expectedBool, 1,
            ],
            'array of one array of strings' => [
                $arrayItems, null, null, false, $expectedArray, 1,
            ],
            'array of one object' => [
                $objectItems, null, null, false, $expectedObject, 1
            ],
        ];
    }

    /**
     * @dataProvider provideMockArrayInvalidArguments
     * @covers ::mockArray
     */
    public function testMockArrayWithInvalidArguments(
        $items,
        $minItems,
        $maxItems,
        $uniqueItems
    ) {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $arr = $mocker->mockArray($items, $minItems, $maxItems, $uniqueItems);
    }

    public function provideMockArrayInvalidArguments()
    {
        $intItems = ['type' => IMocker::DATA_TYPE_INTEGER];

        return [
            'minItems is negative' => [
                $intItems, -10, null, false,
            ],
            'maxItems is negative' => [
                $intItems, null, -10, false,
            ],
            'maxItems less than minItems' => [
                $intItems, 5, 2, false,
            ],
            'items with ref to unknown class' => [
                ['$ref' => '#/components/schemas/UnknownClass'], null, null, false,
            ],
            'items with ref to class without getOpenApiSchema method' => [
                ['$ref' => '#/components/schemas/ClassWithoutGetSchemaMethod'], null, null, false,
            ],
        ];
    }

    /**
     * @dataProvider provideMockArrayWithRefArguments
     * @covers ::mockArray
     */
    public function testMockArrayWithRef($items, $expectedStructure)
    {
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $arr = $mocker->mockArray($items);
        $this->assertIsArray($arr);
        $this->assertCount(1, $arr);
        foreach ($arr as $item) {
            $this->assertNotInstanceOf('OpenAPIServer\\Mock\\Model\\CatRefTestClass', $item);
            foreach ($expectedStructure as $expectedProp => $assertMethod) {
                $this->$assertMethod($item->$expectedProp);
            }
        }
    }

    public function provideMockArrayWithRefArguments()
    {
        return [
            'items with ref to CatRefTestClass' => [
                ['$ref' => '#/components/schemas/CatRefTestClass'],
                [
                    'className' => 'assertIsString',
                    'color' => 'assertIsString',
                    'declawed' => 'assertIsBool',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideMockObjectCorrectArguments
     * @covers ::mockObject
     */
    public function testMockObjectWithCorrectArguments(
        $properties,
        $minProperties,
        $maxProperties,
        $additionalProperties,
        $required,
        $expectedKeys
    ) {
        $mocker = new OpenApiDataMocker();
        $obj = $mocker->mockObject(
            $properties,
            $minProperties,
            $maxProperties,
            $additionalProperties,
            $required
        );

        $this->assertIsObject($obj);
        $this->assertSame($expectedKeys, array_keys(get_object_vars($obj)));
    }

    public function provideMockObjectCorrectArguments()
    {
        $additionProps = [
            'extra' => [
                'type' => IMocker::DATA_TYPE_STRING,
            ],
        ];
        return [
            'empty object' => [
                [], 1, 10, true, null, [],
            ],
            'object with username property' => [
                [
                    'username' => [
                        'type' => IMocker::DATA_TYPE_STRING,
                    ],
                ], 0, 5, $additionProps, null, ['username'],
            ],
            'object with foobar property' => [
                [
                    'foobar' => [
                        'type' => IMocker::DATA_TYPE_INTEGER,
                    ],
                ], 1, 1, (object) $additionProps, null, ['foobar'],
            ],
        ];
    }

    /**
     * @dataProvider provideMockObjectInvalidArguments
     * @covers ::mockObject
     */
    public function testMockObjectWithInvalidArguments(
        $properties,
        $minProperties,
        $maxProperties,
        $additionalProperties,
        $required
    ) {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $obj = $mocker->mockObject($properties, $minProperties, $maxProperties, $additionalProperties, $required);
    }

    public function provideMockObjectInvalidArguments()
    {
        return [
            'property value cannot be a string' => [
                ['username' => 'foobar'], 0, 10, false, null,
            ],
            'minProperties is negative' => [
                [], -10, null, false, null,
            ],
            'maxProperties is negative' => [
                [], null, -10, false, null,
            ],
            'maxProperties less than minProperties' => [
                [], 5, 2, false, null,
            ],
            'additionalProperties is not object|array|boolean' => [
                [], null, null, 'foobar', null,
            ],
            'required array with duplicates' => [
                [], null, null, null, ['username', 'username'],
            ],
            'required array of non-strings' => [
                [], null, null, null, [1, 2, 3],
            ],
        ];
    }

    /**
     * @covers ::mockObject
     */
    public function testMockObjectWithReferencedProps()
    {
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $obj = $mocker->mockObject(
            [
                'cat' => [
                    '$ref' => '#/components/schemas/CatRefTestClass',
                ],
            ]
        );
        $this->assertIsObject($obj->cat);
        $this->assertIsString($obj->cat->className);
        $this->assertIsString($obj->cat->color);
        $this->assertIsBool($obj->cat->declawed);
    }

    /**
     * @dataProvider provideMockFromSchemaCorrectArguments
     * @covers ::mockFromSchema
     */
    public function testMockFromSchemaWithCorrectArguments($schema, $assertMethod)
    {
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $data = $mocker->mockFromSchema($schema);
        $this->$assertMethod($data);
    }

    public function provideMockFromSchemaCorrectArguments()
    {
        return [
            'string from array' => [
                ['type' => IMocker::DATA_TYPE_STRING],
                'assertIsString',
            ],
            'integer from array' => [
                ['type' => IMocker::DATA_TYPE_INTEGER],
                'assertIsInt',
            ],
            'number from array' => [
                ['type' => IMocker::DATA_TYPE_NUMBER],
                'assertIsFloat',
            ],
            'string from array' => [
                ['type' => IMocker::DATA_TYPE_STRING],
                'assertIsString',
            ],
            'boolean from array' => [
                ['type' => IMocker::DATA_TYPE_BOOLEAN],
                'assertIsBool',
            ],
            'array of strings from array' => [
                [
                    'type' => IMocker::DATA_TYPE_ARRAY,
                    'items' => ['type' => IMocker::DATA_TYPE_STRING],
                ],
                'assertIsArray',
            ],
            'object with username prop from array' => [
                [
                    'type' => IMocker::DATA_TYPE_OBJECT,
                    'properties' => ['username' => ['type' => IMocker::DATA_TYPE_STRING]],
                ],
                'assertIsObject',
            ],
            'referenced class' => [
                ['$ref' => '#/components/schemas/CatRefTestClass'],
                'assertIsObject',
            ],
        ];
    }

    /**
     * @dataProvider provideMockFromSchemaInvalidArguments
     * @covers ::mockFromSchema
     */
    public function testMockFromSchemaWithInvalidArguments($schema)
    {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $data = $mocker->mockFromSchema($schema);
    }


    public function provideMockFromSchemaInvalidArguments()
    {
        return [
            'empty array' => [[]],
        ];
    }

    /**
     * @dataProvider provideMockFromRefCorrectArguments
     * @covers ::mockFromRef
     * @covers ::mockModelFromRef
     * @covers ::refToModelClass
     */
    public function testMockFromRefWithCorrectArguments($ref, $expectedStructure)
    {
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $data = $mocker->mockFromRef($ref);
        $model = $mocker->mockModelFromRef($ref);
        if ($data && $model) {
            foreach ($expectedStructure as $expectedProp => $assertMethod) {
                $this->$assertMethod($data->$expectedProp);
                $this->$assertMethod($model->jsonSerialize()->$expectedProp);
            }
        }

        if (empty($ref)) {
            $this->assertNull($data);
            $this->assertNull($model);
        }
    }

    public function provideMockFromRefCorrectArguments()
    {
        return [
            'CatRefTestClass model' => [
                '#/components/schemas/CatRefTestClass',
                [
                    'className' => 'assertIsString',
                    'color' => 'assertIsString',
                    'declawed' => 'assertIsBool',
                ]
            ],
            'empty string' => [
                '',
                null,
            ],
        ];
    }

    /**
     * @dataProvider provideMockFromRefInvalidArguments
     * @covers ::mockFromRef
     * @covers ::refToModelClass
     */
    public function testMockFromRefWithInvalidArguments($ref)
    {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $data = $mocker->mockFromRef($ref);
    }

    /**
     * @dataProvider provideMockFromRefInvalidArguments
     * @covers ::mockModelFromRef
     * @covers ::refToModelClass
     */
    public function testMockModelFromRefWithInvalidArguments($ref)
    {
        $this->expectException(InvalidArgumentException::class);
        $mocker = new OpenApiDataMocker();
        $mocker->setModelsNamespace('OpenAPIServer\\Mock\\Model\\');
        $model = $mocker->mockModelFromRef($ref);
    }

    public function provideMockFromRefInvalidArguments()
    {
        return [
            'ref to unknown class' => ['#/components/schemas/UnknownClass'],
            'ref to class without getOpenApiSchema method' => ['#/components/schemas/ClassWithoutGetSchemaMethod'],
            'ref to class doesn\'t implement model OpenApiModelInterface' => ['#/components/schemas/ClassNotImplementModelInterface'],
        ];
    }

    /**
     * @covers ::setModelsNamespace
     * @covers ::getModelsNamespace
     */
    public function testSetModelsNamespaceSetterAndGetterWithCorrectArguments()
    {
        $mocker = new OpenApiDataMocker();
        $this->assertNull($mocker->getModelsNamespace());
        $mocker->setModelsNamespace('JohnDoesPackage\\Model\\');
        $this->assertSame('JohnDoesPackage\\Model\\', $mocker->getModelsNamespace());
        // reset namespace
        $mocker->setModelsNamespace();
        $this->assertNull($mocker->getModelsNamespace());
    }
}
