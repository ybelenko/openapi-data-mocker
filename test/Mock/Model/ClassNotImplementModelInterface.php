<?php

namespace OpenAPIServer\Mock\Model;

class ClassNotImplementModelInterface
{
    public static function getOpenApiSchema()
    {
        return [];
    }

    public static function createFromData($data)
    {
        return new static();
    }
}
