<?php

namespace OpenAPIServer\Mock;

use PHPUnit\Framework\TestCase;
use OpenAPIServer\Mock\BaseModel;
use OpenAPIServer\Mock\Model\CatRefTestClass;
use OpenAPIServer\Mock\Model\DogRefTestClass;
use OpenAPIServer\Mock\OpenApiModelInterface;
use InvalidArgumentException;

/**
 * @coversDefaultClass \OpenAPIServer\Mock\BaseModel
 */
class BaseModelTest extends TestCase
{
    /**
     * @covers ::getOpenApiSchema
     */
    public function testGetOpenApiSchema()
    {
        foreach (
            [
                BaseModel::getOpenApiSchema(),
                CatRefTestClass::getOpenApiSchema(),
                DogRefTestClass::getOpenApiSchema(),
            ] as $schema
        ) {
            $this->assertTrue(is_array($schema) || is_object($schema));
        }
    }

    /**
     * @covers ::createFromData
     * @covers ::__set
     * @covers ::__get
     * @dataProvider provideCreateFromDataArguments
     */
    public function testCreateFromData($modelClass, $data)
    {
        $item = $modelClass::createFromData($data);
        $this->assertInstanceOf($modelClass, $item);
        $this->assertInstanceOf(OpenApiModelInterface::class, $item);
        foreach ($data as $propName => $propValue) {
            $this->assertSame($propValue, $item->{$propName});
        }
    }

    public function provideCreateFromDataArguments()
    {
        return [
            'CatRefTestClass' => [
                CatRefTestClass::class,
                [
                    'className' => 'cheshire',
                    'color' => 'gray',
                    'declawed' => true,
                ],
            ],
            'DogRefTestClass' => [
                DogRefTestClass::class,
                [
                    'className' => 'bulldog',
                    'color' => 'black',
                    'declawed' => false,
                ],
            ],
        ];
    }

    /**
     * @covers ::__set
     * @covers ::__get
     */
    public function testSetter()
    {
        $item = new CatRefTestClass();
        $item->className = 'cheshire';
        $item->color = 'black';
        $item->declawed = false;
        $this->assertSame('cheshire', $item->className);
        $this->assertSame('black', $item->color);
        $this->assertSame(false, $item->declawed);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot set unknownProp property of OpenAPIServer\Mock\Model\CatRefTestClass model because it doesn't exist in related OAS schema
     * @covers ::__set
     */
    public function testSetterWithUnknownProp()
    {
        $item = new CatRefTestClass();
        $item->unknownProp = 'foobar';
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot get unknownProp property of OpenAPIServer\Mock\Model\CatRefTestClass model because it doesn't exist in related OAS schema
     * @covers ::__get
     */
    public function testGetterWithUnknownProp()
    {
        $item = new CatRefTestClass();
        $unknownProp = $item->unknownProp;
    }

    /**
     * @covers ::jsonSerialize
     * @dataProvider provideJsonSerializeArguments
     */
    public function testJsonSerialize($className, $data, $expectedJson)
    {
        $item = $className::createFromData($data);
        $this->assertEquals($expectedJson, json_encode($item));
    }

    public function provideJsonSerializeArguments()
    {
        return [
            'model with all props' => [
                CatRefTestClass::class,
                [
                    'className' => 'cheshire',
                    'color' => 'black',
                    'declawed' => false,
                ],
                json_encode([
                    'className' => 'cheshire',
                    'color' => 'black',
                    'declawed' => false,
                ]),
            ],
            'model with required props' => [
                CatRefTestClass::class,
                [
                    'className' => 'cheshire',
                ],
                json_encode([
                    'className' => 'cheshire',
                ]),
            ],
            'model with missed required prop' => [
                CatRefTestClass::class,
                [
                    'color' => 'black',
                ],
                json_encode([
                    'className' => null,
                    'color' => 'black',
                ]),
            ],
            'model with schema serialized as assoc array' => [
                DogRefTestClass::class,
                [
                    'className' => 'bulldog',
                    'color' => 'gray',
                    'declawed' => false,
                ],
                json_encode([
                    'className' => 'bulldog',
                    'color' => 'gray',
                    'declawed' => false,
                ]),
            ],
        ];
    }
}
