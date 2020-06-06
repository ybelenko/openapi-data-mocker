<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\BaseModel;

/**
 * This is test model which returns OAS as assoc array.
 */
class DogRefTestClass extends BaseModel
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{
    "required" : [ "className" ],
    "type" : "object",
    "properties" : {
        "className" : {
            "type" : "string"
        },
        "color" : {
            "type" : "string",
            "default" : "black"
        },
        "declawed" : {
            "type" : "boolean"
        }
    },
    "discriminator" : {
        "propertyName" : "className"
    }
}
SCHEMA;

    /**
     * @inheritdoc Override static method.
     */
    public static function getOpenApiSchema(): array
    {
        // return assoc array instead of object for test purpose
        return json_decode(static::MODEL_SCHEMA, true);
    }
}
