<?php

namespace OpenAPIServer\Mock;

use OpenAPIServer\Mock\OpenApiModelInterface;
use InvalidArgumentException;
use StdClass;

class BaseModel implements OpenApiModelInterface
{
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
     * @var array Data container.
     * PHP has restrictions on variable names, while OAS is much more permissive.
     * This container helps to store unusual properties like '123_prop' without renaming.
     */
    protected $dataContainer = [];

    /**
     * Gets OAS 3.0 schema mapped to current class.
     *
     * @return array|object
     */
    public static function getOpenApiSchema()
    {
        return json_decode(static::MODEL_SCHEMA);
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
        foreach ($data as $key => $value) {
            // this action handles __set method
            $instance->{$key} = $value;
        }
        return $instance;
    }

    /**
     * Writes data to inaccessible (protected or private) or non-existing properties.
     * @ref https://www.php.net/manual/en/language.oop5.overloading.php#object.set
     *
     * @param string $param Property name
     * @param mixed  $value Property value
     *
     * @throws \InvalidArgumentException when property doesn't exist in related OAS schema
     */
    public function __set($param, $value)
    {
        $schema = static::getOpenApiSchema();
        $definedProps = (property_exists($schema, 'properties')) ? $schema->properties : null;
        if (
            is_array($definedProps)
            && in_array($param, array_keys($definedProps))
        ) {
            $this->dataContainer[$param] = $value;
            return;
        } elseif (
            is_object($definedProps)
            && property_exists($definedProps, $param)
        ) {
            $this->dataContainer[$param] = $value;
            return;
        }

        throw new InvalidArgumentException(
            sprintf('Cannot set %s property of %s model because it doesn\'t exist in related OAS schema', $param, static::class)
        );
    }

    /**
     * Reads data from inaccessible (protected or private) or non-existing properties.
     * @ref https://www.php.net/manual/en/language.oop5.overloading.php#object.get
     *
     * @param string $param Property name
     *
     * @throws \InvalidArgumentException when property doesn't exist in related OAS schema
     *
     * @return mixed Property value
     */
    public function __get($param)
    {
        $schema = static::getOpenApiSchema();
        $definedProps = (property_exists($schema, 'properties')) ? $schema->properties : [];
        if (property_exists($definedProps, $param)) {
            return $this->dataContainer[$param];
        }

        throw new InvalidArgumentException(
            sprintf('Cannot get %s property of %s model because it doesn\'t exist in related OAS schema', $param, static::class)
        );
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @ref https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $obj = new StdClass();
        $schema = static::getOpenApiSchema();
        $definedProps = (property_exists($schema, 'properties')) ? $schema->properties : [];
        foreach ($definedProps as $propName => $propSchema) {
            if (array_key_exists($propName, $this->dataContainer)) {
                $obj->{$propName} = $this->dataContainer[$propName];
            } elseif (property_exists($schema, 'required') && in_array($propName, $schema->required)) {
                // property is required but not set
                $obj->{$propName} = null;
            }
        }
        return $obj;
    }
}
