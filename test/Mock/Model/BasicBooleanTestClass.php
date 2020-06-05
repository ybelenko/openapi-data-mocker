<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\BaseModel;

class BasicBooleanTestClass extends BaseModel
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{
    "type" : "boolean"
}
SCHEMA;
}
