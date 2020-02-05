<?php

/**
* OpenApiDataMocker
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

use OpenAPIServer\Mock\OpenApiDataMockerInterface as IMocker;
use InvalidArgumentException;

/**
 * OpenApiDataMocker Class Doc Comment
 *
 * @package OpenAPIServer\Mock
 * @author  OpenAPI Generator team
 * @link    https://github.com/openapitools/openapi-generator
 */
final class OpenApiDataMocker implements IMocker
{
    /**
     * Mocks OpenApi Data.
     * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.1.md#data-types
     *
     * @param $dataType   string     OpenApi data type. Use constants from OpenApiDataMockerInterface class
     * @param $dataFormat string     (optional) OpenApi data format
     * @param $options    array|null (optional) OpenApi data options
     *
     * @throws \InvalidArgumentException when invalid arguments passed
     *
     * @return mixed
     */
    public function mock($dataType, $dataFormat = null, $options = [])
    {
        switch ($dataType) {
            case IMocker::DATA_TYPE_INTEGER:
            case IMocker::DATA_TYPE_NUMBER:
                $minimum = $options['minimum'] ?? null;
                $maximum = $options['maximum'] ?? null;
                $exclusiveMinimum = $options['exclusiveMinimum'] ?? false;
                $exclusiveMaximum = $options['exclusiveMaximum'] ?? false;
                if ($dataType === IMocker::DATA_TYPE_INTEGER) {
                    return $this->mockInteger($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
                }
                return $this->mockNumber($dataFormat, $minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum);
            case IMocker::DATA_TYPE_STRING:
                $minLength = $options['minLength'] ?? 0;
                $maxLength = $options['maxLength'] ?? null;
                return $this->mockString($dataFormat, $minLength, $maxLength);
            case IMocker::DATA_TYPE_BOOLEAN:
                return $this->mockBoolean();
            default:
                throw new InvalidArgumentException('"dataType" must be one of ' . implode(', ', [
                    IMocker::DATA_TYPE_INTEGER,
                    IMocker::DATA_TYPE_NUMBER,
                    IMocker::DATA_TYPE_STRING,
                    IMocker::DATA_TYPE_BOOLEAN,
                ]));
        }
    }

    /**
     * Shortcut to mock integer type
     * Equivalent to mockData(DATA_TYPE_INTEGER);
     *
     * @param string|null $dataFormat       (optional) int32 or int64
     * @param number|null $minimum          (optional) Default is 0
     * @param number|null $maximum          (optional) Default is mt_getrandmax()
     * @param bool|null   $exclusiveMinimum (optional) Default is false
     * @param bool|null   $exclusiveMaximum (optional) Default is false
     *
     * @throws \InvalidArgumentException when $maximum less than $minimum or invalid arguments provided
     *
     * @return int
     */
    public function mockInteger(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        return $this->getRandomNumber($minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum, 0);
    }

    /**
     * Shortcut to mock number type
     * Equivalent to mockData(DATA_TYPE_NUMBER);
     *
     * @param string|null $dataFormat       (optional) float or double
     * @param number|null $minimum          (optional) Default is 0
     * @param number|null $maximum          (optional) Default is mt_getrandmax()
     * @param bool|null   $exclusiveMinimum (optional) Default is false
     * @param bool|null   $exclusiveMaximum (optional) Default is false
     *
     * @throws \InvalidArgumentException when $maximum less than $minimum or invalid arguments provided
     *
     * @return float
     */
    public function mockNumber(
        $dataFormat = null,
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false
    ) {
        return $this->getRandomNumber($minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum, 4);
    }

    /**
     * Shortcut to mock string type
     * Equivalent to mockData(DATA_TYPE_STRING);
     *
     * @param string|null $dataFormat (optional) one of byte, binary, date, date-time, password
     * @param int|null    $minLength  (optional) Default is 0
     * @param int|null    $maxLength  (optional) Default is 100 chars
     * @param array       $enum       (optional) This array should have at least one element.
     * Elements in the array should be unique.
     * @param string|null $pattern    (optional) This string should be a valid regular expression, according to the ECMA 262 regular expression dialect.
     * Recall: regular expressions are not implicitly anchored.
     *
     * @throws \InvalidArgumentException when invalid arguments passed
     *
     * @return string
     */
    public function mockString(
        $dataFormat = null,
        $minLength = 0,
        $maxLength = null,
        $enum = null,
        $pattern = null
    ) {
        if ($minLength !== 0 && $minLength !== null) {
            if (is_int($minLength) === false) {
                throw new InvalidArgumentException('"minLength" must be an integer');
            } elseif ($minLength < 0) {
                throw new InvalidArgumentException('"minLength" must be greater than, or equal to, 0');
            }
        } else {
            $minLength = 0;
        }

        if ($maxLength !== null) {
            if (is_int($maxLength) === false) {
                throw new InvalidArgumentException('"maxLength" must be an integer');
            } elseif ($maxLength < 0) {
                throw new InvalidArgumentException('"maxLength" must be greater than, or equal to, 0');
            }
        } else {
            // since we don't need huge texts by default, lets cut them down to 100 chars
            $maxLength = 100;
        }

        if ($maxLength < $minLength) {
            throw new InvalidArgumentException('"maxLength" value cannot be less than "minLength"');
        }

        return str_pad('', mt_rand($minLength, $maxLength), 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', \STR_PAD_RIGHT);
    }

    /**
     * Shortcut to mock boolean type
     * Equivalent to mockData(DATA_TYPE_BOOLEAN);
     *
     * @return bool
     */
    public function mockBoolean()
    {
        return (bool) mt_rand(0, 1);
    }

    /**
     * @internal
     *
     * @return float|int
     */
    protected function getRandomNumber(
        $minimum = null,
        $maximum = null,
        $exclusiveMinimum = false,
        $exclusiveMaximum = false,
        $maxDecimals = 4
    ) {
        $min = 0;
        $max = mt_getrandmax();

        if ($minimum !== null) {
            if (is_numeric($minimum) === false) {
                throw new InvalidArgumentException('"minimum" must be a number');
            }
            $min = $minimum;
        }

        if ($maximum !== null) {
            if (is_numeric($maximum) === false) {
                throw new InvalidArgumentException('"maximum" must be a number');
            }
            $max = $maximum;
        }

        if ($exclusiveMinimum !== false) {
            if (is_bool($exclusiveMinimum) === false) {
                throw new InvalidArgumentException('"exclusiveMinimum" must be a boolean');
            } elseif ($minimum === null) {
                throw new InvalidArgumentException('If "exclusiveMinimum" is present, "minimum" must also be present');
            }
            $min += 1;
        }

        if ($exclusiveMaximum !== false) {
            if (is_bool($exclusiveMaximum) === false) {
                throw new InvalidArgumentException('"exclusiveMaximum" must be a boolean');
            } elseif ($maximum === null) {
                throw new InvalidArgumentException('If "exclusiveMaximum" is present, "maximum" must also be present');
            }
            $max -= 1;
        }

        if ($max < $min) {
            throw new InvalidArgumentException('"maximum" value cannot be less than "minimum"');
        }

        if ($maxDecimals > 0) {
            return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $maxDecimals);
        }
        return mt_rand($min, $max);
    }
}
