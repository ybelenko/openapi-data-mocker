<?php

/**
 * Openapi Data Mocker
 * PHP version 7.3
 *
 * @package OpenAPIServer\Mock
 * @link    https://github.com/ybelenko/openapi-data-mocker
 * @author  Yuriy Belenko <yura-bely@mail.ru>
 * @license MIT
 */

declare(strict_types=1);

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\OpenApiModelInterface;

/**
 * BaseModelExample.
 * That's example implementation of OpenApiModelInterface.
 * This class isn't part of this package, developers shouldn't use it, but create own implementation of OpenApiModelInterface.
 */
class BaseModelExample implements OpenApiModelInterface
{
    // phpcs:disable Generic.Commenting.DocComment

    /**
     * @var string Constant with OAS schema of current class.
     * Should be overwritten by inherited class.
     */
    protected const MODEL_SCHEMA =
    <<<'SCHEMA'
    {
        "type" : "object",
        "properties": {}
    }
SCHEMA;

    /**
     * @var mixed Data container.
     * PHP has restrictions on variable names, while OAS is much more permissive.
     * This container helps to store unusual properties like '123_prop' without renaming.
     */
    public $dataContainer;
    // phpcs:enable

    /**
     * Gets OAS 3.0 schema mapped to current class.
     *
     * @return array
     */
    public static function getOpenApiSchema(): array
    {
        return json_decode(static::MODEL_SCHEMA, true);
    }

    /**
     * Creates new instance from provided data.
     *
     * @param mixed $data Data with values for new instance.
     *
     * @return OpenApiModelInterface
     */
    public static function createFromData($data): OpenApiModelInterface
    {
        $instance = new static();
        $instance->dataContainer = $data;
        return $instance;
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * Ref @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->dataContainer;
    }
}
