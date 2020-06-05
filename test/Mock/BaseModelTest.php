<?php

/**
 * Openapi Data Mocker
 * PHP version 7.1
 *
 * @package OpenAPIServer\Mock
 * @link    https://github.com/ybelenko/openapi-data-mocker
 * @author  Yuriy Belenko <yura-bely@mail.ru>
 * @license MIT
 */

namespace OpenAPIServer\Mock;

use PHPUnit\Framework\TestCase;
use OpenAPIServer\Mock\BaseModel;
use OpenAPIServer\Mock\Model\CatRefTestClass;
use OpenAPIServer\Mock\Model\DogRefTestClass;
use OpenAPIServer\Mock\Model\BasicArrayTestClass;
use OpenAPIServer\Mock\Model\BasicBooleanTestClass;
use OpenAPIServer\Mock\Model\BasicIntegerTestClass;
use OpenAPIServer\Mock\Model\BasicNumberTestClass;
use OpenAPIServer\Mock\Model\BasicObjectTestClass;
use OpenAPIServer\Mock\Model\BasicStringTestClass;
use OpenAPIServer\Mock\Model\MissingTypeTestClass;
use OpenAPIServer\Mock\Model\UnknownTypeTestClass;
use OpenAPIServer\Mock\OpenApiModelInterface;
use InvalidArgumentException;
use StdClass;

/**
 * BaseModelTest
 *
 * @coversDefaultClass \OpenAPIServer\Mock\BaseModel
 */
class BaseModelTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::validateModelType
     * @dataProvider provideClassesAndDefaultData
     */
    public function testConstructorAndDefaultData($className, $expectedJson)
    {
        $item = new $className();
        $this->assertEquals($expectedJson, json_encode($item->getData()));
    }

    public function provideClassesAndDefaultData()
    {
        return [
            'boolean model' => [BasicBooleanTestClass::class, json_encode(null)],
            'integer model' => [BasicIntegerTestClass::class, json_encode(null)],
            'number model' => [BasicNumberTestClass::class, json_encode(null)],
            'string model' => [BasicStringTestClass::class, json_encode(null)],
            'array model' => [BasicArrayTestClass::class, json_encode([])],
            'object model' => [BasicObjectTestClass::class, json_encode(new StdClass())],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::validateModelType
     * @dataProvider provideInvalidClasses
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorWithInvalidTypes($className)
    {
        $item = new $className();
    }

    public function provideInvalidClasses()
    {
        return [
            'unknown type model' => [UnknownTypeTestClass::class],
            'missing type model' => [MissingTypeTestClass::class],
        ];
    }

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
                BasicArrayTestClass::getOpenApiSchema(),
                BasicBooleanTestClass::getOpenApiSchema(),
                BasicIntegerTestClass::getOpenApiSchema(),
                BasicNumberTestClass::getOpenApiSchema(),
                BasicObjectTestClass::getOpenApiSchema(),
                BasicStringTestClass::getOpenApiSchema(),
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
     * @covers ::setData
     * @covers ::getData
     * @dataProvider provideScalarModels
     */
    public function testSetDataScalar($className, array $setDataValues, array $expectedDataValues)
    {
        $item = new $className();
        for ($i = 0; $i < count($setDataValues); $i++) {
            if ($i > 0) {
                // value should be previous
                $this->assertSame($expectedDataValues[$i - 1], $item->getData());
            } else {
                // initial value should be null
                $this->assertNull($item->getData());
            }
            $item->setData($setDataValues[$i]);
            // values should be overwritten
            $this->assertSame($expectedDataValues[$i], $item->getData());
        }
    }

    public function provideScalarModels()
    {
        return [
            'boolean model' => [
                BasicBooleanTestClass::class,
                [false, true, false],
                [false, true, false],
            ],
            'integer model' => [
                BasicIntegerTestClass::class,
                [-50, 322, 100500, -1000, 0],
                [-50, 322, 100500, -1000, 0],
            ],
            'number model' => [
                BasicNumberTestClass::class,
                [-50.324, 322.756, 100500.09, -1000.43, 0],
                [-50.324, 322.756, 100500.09, -1000.43, 0],
            ],
            'string model' => [
                BasicStringTestClass::class,
                ['foobar', 'hello world', '100', '-56', 'true', 'null', 'false'],
                ['foobar', 'hello world', '100', '-56', 'true', 'null', 'false'],
            ],
        ];
    }

    /**
     * @covers ::setData
     * @covers ::getData
     */
    public function testSetDataOfArray()
    {
        $basic = new BasicArrayTestClass();
        $data = ['foo', 'bar', 'baz'];
        $basic->setData($data);
        $this->assertEquals($data, $basic->getData());
    }

    /**
     * @covers ::setData
     * @dataProvider provideInvalidDataForArrayModel
     * @expectedException \InvalidArgumentException
     */
    public function testSetDataOfArrayWithInvalidData($className, $data)
    {
        $item = new $className();
        $item->setData($data);
    }

    public function provideInvalidDataForArrayModel()
    {
        $obj = new StdClass();
        $obj->foo = 'bar';
        $obj->baz = 'baf';
        $arr = [];
        $arr[5] = 'foo';
        $arr[6] = 'bar';
        return [
            'array with spaced indexes data' => [
                BasicArrayTestClass::class,
                $arr,
            ],
            'assoc array data' => [
                BasicArrayTestClass::class,
                ['foo' => 'bar', 'baz' => 'baf'],
            ],
            'object data' => [
                BasicArrayTestClass::class,
                $obj,
            ],
        ];
    }

    /**
     * @covers ::setData
     * @covers ::getData
     */
    public function testSetDataOfObject()
    {
        $basic = new BasicObjectTestClass();
        $data = ['foo' => 'bar'];
        $basic->setData($data);
        $this->assertSame('bar', $basic->foo);
    }

    /**
     * @covers ::getData
     */
    public function testGetDataOfObject()
    {
        $catItem = new CatRefTestClass();
        $catItem->setData([
            'color' => 'grey',
            'declawed' => false,
        ]);
        $data = $catItem->getData();
        $this->assertInstanceOf(StdClass::class, $data);
        $this->assertSame('grey', $data->color);
        $this->assertSame(false, $data->declawed);
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
     * @covers ::__set
     * @expectedException \InvalidArgumentException
     * @dataProvider provideScalarsAndArray
     */
    public function testSetterOfScalarAndArray($className)
    {
        $item = new $className();
        $item->foo = 'bar';
    }

    public function provideScalarsAndArray()
    {
        return [
            'boolean model' => [BasicBooleanTestClass::class],
            'integer model' => [BasicIntegerTestClass::class],
            'number model' => [BasicNumberTestClass::class],
            'string model' => [BasicStringTestClass::class],
            'array model' => [BasicArrayTestClass::class],
        ];
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
     * @covers ::__get
     * @expectedException \InvalidArgumentException
     * @dataProvider provideScalarsAndArray
     */
    public function testGetterOfScalarAndArray($className)
    {
        $item = new $className();
        $bar = $item->foo;
    }

    /**
     * @covers ::__set
     * @covers ::__get
     */
    public function testSetterAndGetterOfBasicObject()
    {
        $item = new BasicObjectTestClass();
        $item->unknown = 'foo';
        $this->assertEquals('foo', $item->unknown);
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
            'model of basic array' => [
                BasicArrayTestClass::class,
                ['hello', 'world'],
                json_encode(['hello', 'world']),
            ],
            'model of basic boolean' => [
                BasicBooleanTestClass::class,
                false,
                json_encode(false),
            ],
            'model of basic integer' => [
                BasicIntegerTestClass::class,
                -500,
                json_encode(-500),
            ],
            'model of basic number' => [
                BasicNumberTestClass::class,
                -3.1434,
                json_encode(-3.1434),
            ],
            'model of basic object' => [
                BasicObjectTestClass::class,
                new \StdClass(),
                json_encode(new \StdClass()),
            ],
            'model of basic string' => [
                BasicStringTestClass::class,
                'foobar',
                json_encode('foobar'),
            ],
        ];
    }
}
