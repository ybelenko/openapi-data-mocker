<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\Model\BaseModelExample;

class UnknownTypeTestClass extends BaseModelExample
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{
    "type" : "foobar"
}
SCHEMA;
}
