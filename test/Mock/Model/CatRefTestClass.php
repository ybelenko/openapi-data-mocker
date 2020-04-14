<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\BaseModel;
use OpenAPIServer\Mock\MockableInterface;

class CatRefTestClass extends BaseModel
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
            "default" : "red"
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
}
