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

use OpenAPIServer\Utils\StringUtilsTrait;

/**
 * ModelUtilsTrait
 *
 * This class duplicates functionality of ModelUtils.java and AbstractPhpCodegen.java classes from Openapi-Generator.
 */
trait ModelUtilsTrait
{
    use StringUtilsTrait;

    /**
     * Parses model class name from provided ref.
     * @link https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.1.md#reference-object
     * This method doesn't check that class exists and autoloaded.
     * This is recreated method of @link modules/openapi-generator/src/main/java/org/openapitools/codegen/utils/ModelUtils.java class.
     *
     * @param string $ref Reference, eg. #/components/schemas/Pet
     *
     * @return string|null classname or null on fail
     */
    public static function getSimpleRef($ref)
    {
        $model = null;
        if (stripos($ref, '#/components/') === 0) {
            // starts with #/components/
            $model = substr($ref, strrpos($ref, '/') + 1);
        } elseif (stripos($ref, '#/definitions/') === 0) {
            // starts with #/definitions/
            $model = substr($ref, strrpos($ref, '/') + 1);
        }

        return $model;
    }

    /**
     * Output the proper model name (capitalized).
     * In case the name belongs to the TypeSystem it won't be renamed.
     * This is recreated method of @link modules/openapi-generator/src/main/java/org/openapitools/codegen/languages/AbstractPhpCodegen.java class.
     *
     * @param string      $name            the name of the model
     * @param string|null $modelNamePrefix modelNamePrefix generator option
     * @param string|null $modelNameSuffix modelNameSuffix generator option
     *
     * @return string capitalized model name
     */
    public static function toModelName(
        $name,
        $modelNamePrefix = null,
        $modelNameSuffix = null
    ) {
        if (is_string($name) === false  || empty($name)) {
            return null;
        }

        // remove [
        $name = str_replace(']', '', $name);

        // Note: backslash ("\\") is allowed for e.g. "\\DateTime"
        $name = preg_replace('/[^\w\\\\]+/', '_', $name); // FIXME: a parameter should not be assigned. Also declare the methods parameters as 'final'.

        // remove underscores from start and end
        $name = trim($name, '_');

        // remove dollar sign
        $name = str_replace('$', '', $name);

        // model name cannot use reserved keyword
        if (self::isReservedWord($name)) {
            $name = 'model_' . $name; // e.g. return => ModelReturn (after camelize)
        }

        // model name starts with number
        if (preg_match('/^\d.*/', $name) === 1) {
            $name = 'model_' . $name; // e.g. 200Response => Model200Response (after camelize)
        }

        // add prefix and/or suffic only if name does not start wth \ (e.g. \DateTime)
        if (preg_match('/^\\\\.*/', $name) !== 1) {
            if (is_string($modelNamePrefix) && !empty($modelNamePrefix)) {
                $name = $modelNamePrefix . '_' . $name;
            }

            if (is_string($modelNameSuffix) && !empty($modelNameSuffix)) {
                $name = $name . '_' . $modelNameSuffix;
            }
        }

        // camelize the model name
        // phone_number => PhoneNumber
        return self::camelize($name);
    }
}
