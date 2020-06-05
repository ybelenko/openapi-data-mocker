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

use OpenAPIServer\Mock\OpenApiDataMockerInterface as IMocker;
use OpenAPIServer\Mock\OpenApiModelInterface;
use OpenAPIServer\Utils\ModelUtilsTrait;
use StdClass;
use DateTime;
use InvalidArgumentException;

/**
 * OpenApiDataMocker
 */
class OpenApiDataMocker implements IMocker
{
    use ModelUtilsTrait;

    /** @var string|null Model classes namespace */
    protected $modelsNamespace;

    /**
     * Mocks OpenApi Data. @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.1.md#data-types
     *
     * @param string     $dataType   OpenApi data type. Use constants from this class.
     * @param string     $dataFormat OpenApi data format.
     * @param array|null $options    OpenApi data options.
     *
     * @throws \InvalidArgumentException When invalid arguments passed.
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
                $enum = $options['enum'] ?? null;
                return $this->mockString($dataFormat, $minLength, $maxLength, $enum);
            case IMocker::DATA_TYPE_BOOLEAN:
                return $this->mockBoolean();
            case IMocker::DATA_TYPE_ARRAY:
                $items = $options['items'] ?? null;
                $minItems = $options['minItems'] ?? 0;
                $maxItems = $options['maxItems'] ?? null;
                $uniqueItems = $options['uniqueItems'] ?? false;
                return $this->mockArray($items, $minItems, $maxItems, $uniqueItems);
            case IMocker::DATA_TYPE_OBJECT:
                $properties = $options['properties'] ?? null;
                $minProperties = $options['minProperties'] ?? 0;
                $maxProperties = $options['maxProperties'] ?? null;
                $additionalProperties = $options['additionalProperties'] ?? null;
                $required = $options['required'] ?? null;
                return $this->mockObject($properties, $minProperties, $maxProperties, $additionalProperties, $required);
            default:
                throw new InvalidArgumentException('"dataType" must be one of ' . implode(', ', [
                    IMocker::DATA_TYPE_INTEGER,
                    IMocker::DATA_TYPE_NUMBER,
                    IMocker::DATA_TYPE_STRING,
                    IMocker::DATA_TYPE_BOOLEAN,
                    IMocker::DATA_TYPE_ARRAY,
                    IMocker::DATA_TYPE_OBJECT,
                ]));
        }
    }

    /**
     * Shortcut to mock integer type
     * Equivalent to mockData(DATA_TYPE_INTEGER);
     *
     * @param string|null $dataFormat       Possible values: int32 or int64.
     * @param float|null  $minimum          Default is 0.
     * @param float|null  $maximum          Default is mt_getrandmax().
     * @param bool|null   $exclusiveMinimum Default is false.
     * @param bool|null   $exclusiveMaximum Default is false.
     *
     * @throws \InvalidArgumentException When $maximum less than $minimum or invalid arguments provided.
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
        $dataFormat = is_string($dataFormat) ? strtolower($dataFormat) : $dataFormat;
        switch ($dataFormat) {
            case IMocker::DATA_FORMAT_INT32:
                // -2147483647..2147483647
                $minimum = is_numeric($minimum) ? max($minimum, -2147483647) : -2147483647;
                $maximum = is_numeric($maximum) ? min($maximum, 2147483647) : 2147483647;
                break;
            case IMocker::DATA_FORMAT_INT64:
                // -9223372036854775807..9223372036854775807
                $minimum = is_numeric($minimum) ? max($minimum, -9223372036854775807) : -9223372036854775807;
                $maximum = is_numeric($maximum) ? min($maximum, 9223372036854775807) : 9223372036854775807;
                break;
            default:
                // do nothing, unsupported format
        }

        return $this->getRandomNumber($minimum, $maximum, $exclusiveMinimum, $exclusiveMaximum, 0);
    }

    /**
     * Shortcut to mock number type
     * Equivalent to mockData(DATA_TYPE_NUMBER);
     *
     * @param string|null $dataFormat       Possible values: float or double.
     * @param float|null  $minimum          Default is 0.
     * @param float|null  $maximum          Default is mt_getrandmax().
     * @param bool|null   $exclusiveMinimum Default is false.
     * @param bool|null   $exclusiveMaximum Default is false.
     *
     * @throws \InvalidArgumentException When $maximum less than $minimum or invalid arguments provided.
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
     * @param string|null $dataFormat Possible values: byte, binary, date, date-time, password.
     * @param int|null    $minLength  Default is 0.
     * @param int|null    $maxLength  Default is 100 chars.
     * @param array       $enum       This array should have at least one element.
     *                                Elements in the array should be unique.
     * @param string|null $pattern    This string should be a valid regular expression, according to the ECMA 262 regular expression dialect.
     *                                Recall: regular expressions are not implicitly anchored.
     *
     * @throws \InvalidArgumentException When invalid arguments passed.
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
        $str = '';
        $getLoremIpsum = function ($length) {
            return str_pad(
                '',
                $length,
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ',
                \STR_PAD_RIGHT
            );
        };
        $truncateOrPad = function ($text, $min = null, $max = null, $glue = '') {
            if ($max !== null && mb_strlen($text) > $max) {
                // truncate
                $text = substr($text, 0, $max);
            } elseif ($min !== null && mb_strlen($text) < $min) {
                // pad
                $text = str_pad('', $min, $text . $glue, \STR_PAD_RIGHT);
            }
            return $text;
        };

        if ($enum !== null) {
            if (
                is_array($enum) === false
                || empty($enum)
                || count($enum) > count(array_unique($enum))
            ) {
                throw new InvalidArgumentException('"enum" must be an array. This array should have at least one element. Elements in the array should be unique.');
            }

            // return random variant
            return $enum[mt_rand(0, count($enum) - 1)];
        }

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

        switch ($dataFormat) {
            case IMocker::DATA_FORMAT_BYTE:
            case IMocker::DATA_FORMAT_BINARY:
                // base64 encoded string
                $inputLength = 1;
                $str = base64_encode($getLoremIpsum($inputLength));
                while (mb_strlen($str) < $minLength) {
                    $inputLength++;
                    $str = base64_encode($getLoremIpsum($inputLength));
                }

                // base64 encoding produces strings divided by 4, so resulted string can exceed maxLength parameter
                // I think truncated(invalid) base64 string is better than oversized, cause this data is fake anyway
                $str = $truncateOrPad($str, null, $maxLength, '. ');
                break;
            case IMocker::DATA_FORMAT_DATE:
            case IMocker::DATA_FORMAT_DATE_TIME:
                // min unix timestamp is 0 and max is 2147483647 for 32bit systems which equals 2038-01-19 03:14:07
                $date = DateTime::createFromFormat('U', mt_rand(0, 2147483647));
                $str = ($dataFormat === IMocker::DATA_FORMAT_DATE) ? $date->format('Y-m-d') : $date->format('Y-m-d\TH:i:sP');

                // truncate or pad datestring to fit minLength and maxLength
                $str = $truncateOrPad($str, $minLength, $maxLength, ' ');
                break;
            case IMocker::DATA_FORMAT_PASSWORD:
                // use list of most popular passwords
                $obviousPassList = [
                    'qwerty',
                    'qwerty12345',
                    'hello',
                    '12345',
                    '0000',
                    'qwerty12345!',
                    'qwertyuiop[]',
                ];
                $str = $obviousPassList[mt_rand(0, count($obviousPassList) - 1)];

                // truncate or pad password to fit minLength and maxLength
                $str = $truncateOrPad($str, $minLength, $maxLength);
                break;
            case IMocker::DATA_FORMAT_UUID:
                // use php built-in uniqid function
                $str = uniqid();

                // truncate or pad uuid to fit minLength and maxLength
                $str = $truncateOrPad($str, $minLength, $maxLength);
                break;
            case IMocker::DATA_FORMAT_EMAIL:
                // just for visionary purpose, not related to real persons
                $fakeEmailList = [
                    'johndoe',
                    'lhoswald',
                    'ojsimpson',
                    'mlking',
                    'jfkennedy',
                ];
                $str = $fakeEmailList[mt_rand(0, count($fakeEmailList) - 1)] . '@example.com';

                // truncate or pad email to fit minLength and maxLength
                $str = $truncateOrPad($str, $minLength, $maxLength);
                break;
            default:
                $str = $getLoremIpsum(mt_rand($minLength, $maxLength));
        }

        return $str;
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
     * Shortcut to mock array type
     * Equivalent to mockData(DATA_TYPE_ARRAY);
     *
     * @param array     $items       Assoc array of described items.
     * @param int|null  $minItems    An array instance is valid against "minItems" if its size is greater than, or equal to, the value of this keyword.
     * @param int|null  $maxItems    An array instance is valid against "maxItems" if its size is less than, or equal to, the value of this keyword.
     * @param bool|null $uniqueItems If it has boolean value true, the instance validates successfully if all of its elements are unique.
     *
     * @throws \InvalidArgumentException When invalid arguments passed.
     *
     * @return array
     */
    public function mockArray(
        $items,
        $minItems = 0,
        $maxItems = null,
        $uniqueItems = false
    ) {
        $arr = [];
        $minSize = 0;
        $maxSize = \PHP_INT_MAX;

        if (
            (is_array($items) === false && is_object($items) === false)
            || (is_array($items) && array_key_exists('type', $items) === false)
            || (is_object($items) && isset($items->type) === false)
        ) {
            new InvalidArgumentException('"items" must be object or assoc array with "type" key');
        }

        if ($minItems !== null) {
            if (is_integer($minItems) === false || $minItems < 0) {
                throw new InvalidArgumentException('"mitItems" must be an integer. This integer must be greater than, or equal to, 0');
            }
            $minSize = $minItems;
        }

        if ($maxItems !== null) {
            if (is_integer($maxItems) === false || $maxItems < 0) {
                throw new InvalidArgumentException('"maxItems" must be an integer. This integer must be greater than, or equal to, 0.');
            }
            if ($maxItems < $minItems) {
                throw new InvalidArgumentException('"maxItems" value cannot be less than "minItems"');
            }
            $maxSize = $maxItems;
        }

        $options = $this->extractSchemaProperties($items);
        $dataType = $options['type'];
        $dataFormat = $options['format'] ?? null;
        $ref = $options['$ref'] ?? null;

        // always generate smallest possible array to avoid huge JSON responses
        $arrSize = ($maxSize < 1) ? $maxSize : max($minSize, 1);
        while (count($arr) < $arrSize) {
            $data = $this->mockFromRef($ref);
            $arr[] = ($data) ? $data : $this->mock($dataType, $dataFormat, $options);
        }
        return $arr;
    }

    /**
     * Shortcut to mock object type.
     * Equivalent to mockData(DATA_TYPE_OBJECT);
     *
     * @param array                  $properties           Assoc array of described properties.
     * @param int|null               $minProperties        An object instance is valid against "minProperties" if its number of properties is greater than, or equal to, the value of this keyword.
     * @param int|null               $maxProperties        An object instance is valid against "maxProperties" if its number of properties is less than, or equal to, the value of this keyword.
     * @param bool|object|array|null $additionalProperties If "additionalProperties" is true, validation always succeeds.
     *                                                     If "additionalProperties" is false, validation succeeds only if the instance is an object and all properties on the instance were covered by "properties" and/or "patternProperties".
     *                                                     If "additionalProperties" is an object, validate the value as a schema to all of the properties that weren't validated by "properties" nor "patternProperties".
     * @param array|null             $required             This array MUST have at least one element.  Elements of this array must be strings, and MUST be unique.
     *                                                     An object instance is valid if its property set contains all elements in this array value.
     *
     * @throws \InvalidArgumentException When invalid arguments passed.
     *
     * @return object
     */
    public function mockObject(
        $properties,
        $minProperties = 0,
        $maxProperties = null,
        $additionalProperties = null,
        $required = null
    ) {
        $obj = new StdClass();

        if (is_object($properties) === false && is_array($properties) === false) {
            throw new InvalidArgumentException('The value of "properties" must be an array or object');
        }

        foreach ($properties as $propName => $propValue) {
            if (is_object($propValue) === false && is_array($propValue) === false) {
                throw new InvalidArgumentException('Each value of "properties" must be an array or object');
            }
        }

        if ($minProperties !== null) {
            if (is_integer($minProperties) === false || $minProperties < 0) {
                throw new InvalidArgumentException('"minProperties" must be an integer. This integer must be greater than, or equal to, 0');
            }
        }

        if ($maxProperties !== null) {
            if (is_integer($maxProperties) === false || $maxProperties < 0) {
                throw new InvalidArgumentException('"maxProperties" must be an integer. This integer must be greater than, or equal to, 0.');
            }
            if ($maxProperties < $minProperties) {
                throw new InvalidArgumentException('"maxProperties" value cannot be less than "minProperties"');
            }
        }

        if ($additionalProperties !== null) {
            if (is_bool($additionalProperties) === false && is_object($additionalProperties) === false && is_array($additionalProperties) === false) {
                throw new InvalidArgumentException('The value of "additionalProperties" must be a boolean or object or array.');
            }
        }

        if ($required !== null) {
            if (
                is_array($required) === false
                || count($required) > count(array_unique($required))
            ) {
                throw new InvalidArgumentException('The value of "required" must be an array. Elements of this array must be unique.');
            }
            foreach ($required as $requiredPropName) {
                if (is_string($requiredPropName) === false) {
                    throw new InvalidArgumentException('Elements of "required" array must be strings');
                }
            }
        }

        foreach ($properties as $propName => $propValue) {
            $options = $this->extractSchemaProperties($propValue);
            $dataType = $options['type'];
            $dataFormat = $options['format'] ?? null;
            $ref = $options['$ref'] ?? null;
            $data = $this->mockFromRef($ref);
            $obj->$propName = ($data) ? $data : $this->mock($dataType, $dataFormat, $options);
        }

        return $obj;
    }

    /**
     * Mocks OpenApi Data from schema.
     *
     * @param array $schema OpenAPI schema.
     *
     * @throws \InvalidArgumentException When invalid arguments passed.
     *
     * @return mixed
     */
    public function mockFromSchema($schema)
    {
        $props = $this->extractSchemaProperties($schema);
        if (array_key_exists('$ref', $props) && !empty($props['$ref'])) {
            return $this->mockFromRef($props['$ref']);
        } elseif ($props['type'] === null) {
            throw new InvalidArgumentException('"schema" must be object or assoc array with "type" property');
        }
        return $this->mock($props['type'], $props['format'], $props);
    }

    /**
     * Mock data by referenced schema.
     *
     * @param string|null $ref Ref to model, eg. #/components/schemas/User
     *
     * @return OpenApiModelInterface
     */
    public function mockFromRef($ref)
    {
        $data = null;
        if (is_string($ref) && !empty($ref)) {
            $refName = static::getSimpleRef($ref);
            $modelName = static::toModelName($refName);
            $modelClass = $this->getModelsNamespace() . $modelName;
            if (!class_exists($modelClass)) {
                throw new InvalidArgumentException(sprintf('Model %s not found', $modelClass));
            } elseif (!method_exists($modelClass, 'getOpenApiSchema')) {
                throw new InvalidArgumentException(sprintf('Method %s doesn\'t exist', $modelClass . '::getOpenApiSchema'));
            }
            $data = $this->mockFromSchema($modelClass::getOpenApiSchema());
            $data = $modelClass::createFromData($data);
        }

        return $data;
    }

    /**
     * @internal Extract OAS properties from array or object.
     * @codeCoverageIgnore
     *
     * @param array|object $val Processed array or object
     *
     * @return array
     */
    private function extractSchemaProperties($val)
    {
        $props = [
            'type' => null,
            'format' => null,
        ];
        foreach (
            [
                'type',
                'format',
                'minimum',
                'maximum',
                'exclusiveMinimum',
                'exclusiveMaximum',
                'minLength',
                'maxLength',
                'pattern',
                'enum',
                'items',
                'minItems',
                'maxItems',
                'uniqueItems',
                'properties',
                'minProperties',
                'maxProperties',
                'additionalProperties',
                'required',
                'example',
                '$ref',
            ] as $propName
        ) {
            if (is_array($val) && array_key_exists($propName, $val)) {
                $props[$propName] = $val[$propName];
            } elseif (is_object($val) && isset($val->$propName)) {
                $props[$propName] = $val->$propName;
            }
        }
        return $props;
    }

    /**
     * @internal
     * @codeCoverageIgnore
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
        return mt_rand((int) $min, (int) $max);
    }

    /**
     * Sets models namespace for handling $ref links.
     *
     * @param string|null $namespace Namespace of model classes eg. JohnDoesPackage\\Model\\
     *
     * @throws \InvalidArgumentException when namespace not a string or null
     */
    public function setModelsNamespace($namespace = null)
    {
        if ($namespace !== null && !is_string($namespace)) {
            throw new InvalidArgumentException('"namespace" must be a string or null');
        }

        $this->modelsNamespace = $namespace;
    }

    /**
     * Gets models namespace.
     *
     * @return string|null
     */
    public function getModelsNamespace()
    {
        return $this->modelsNamespace;
    }
}
