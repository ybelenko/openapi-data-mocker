<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OpenAPIServer\Mock\OpenapiDataMocker as Mocker;

$mocker = new Mocker();
// set model classes namespace for $ref handling
$mocker->setModelsNamespace('JohnDoesPackage\\Model\\');

// integer from 1 to 100
echo 'Integer from 1 to 100' . PHP_EOL;
echo $mocker->mockInteger(null, 1, 100);

// float from -3 to 3
echo PHP_EOL . PHP_EOL . 'Float from -3 to 3' . PHP_EOL;
echo  $mocker->mockNumber(null, -3, 3);

// string 10 chars
echo PHP_EOL . PHP_EOL . 'String 10 chars' . PHP_EOL;
echo $mocker->mockString(null, 10, 10);

// boolean
echo PHP_EOL . PHP_EOL . 'Boolean' . PHP_EOL;
echo $mocker->mockBoolean() ? 'TRUE' : 'FALSE';

// array of strings
echo PHP_EOL . PHP_EOL . 'Array of strings' . PHP_EOL;
$items = ['type' => 'string', 'maxLength' => 20];
echo json_encode($mocker->mockArray($items), \JSON_PRETTY_PRINT);

// object
echo PHP_EOL . PHP_EOL . 'Object' . PHP_EOL;
$props = ['id' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10], 'username' => ['type' => 'string', 'maxlength' => 10]];
echo json_encode($mocker->mockObject($props), \JSON_PRETTY_PRINT);

echo PHP_EOL . PHP_EOL . 'Real world schema' . PHP_EOL ;
$schema = \OpenAPIServer\Mock\Model\InvoiceTest::getOpenApiSchema();
$data = $mocker->mockSchemaObject($schema);
echo json_encode($data, \JSON_PRETTY_PRINT);
