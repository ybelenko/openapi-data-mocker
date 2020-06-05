<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\BaseModel;

class MissingTypeTestClass extends BaseModel
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{}
SCHEMA;
}
