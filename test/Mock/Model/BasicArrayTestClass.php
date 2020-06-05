<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\BaseModel;

class BasicArrayTestClass extends BaseModel
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{
    "type" : "array"
}
SCHEMA;
}
