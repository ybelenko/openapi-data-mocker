<?php

namespace OpenAPIServer\Mock\Model;

use OpenAPIServer\Mock\Model\BaseModelExample;

// real world complex schema
class InvoiceTest extends BaseModelExample
{
    protected const MODEL_SCHEMA = <<<'SCHEMA'
{
    "description": "Real world example schema",
    "type" : "object",
    "properties": {
        "id": {
            "type": "integer",
            "format": "int32",
            "minimum": 1
        },
        "purchased_items": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "SKU": {
                        "type": "string",
                        "format": "uuid",
                        "maxLength": 20
                    },
                    "quantity": {
                        "type": "integer",
                        "format": "int32",
                        "minimum": 1,
                        "maximum": 5
                    },
                    "price": {
                        "type": "object",
                        "properties": {
                            "currency": {
                                "type": "string",
                                "minLength": 3,
                                "maxLength": 3,
                                "enum": [
                                    "USD",
                                    "EUR",
                                    "RUB"
                                ]
                            },
                            "value": {
                                "type": "number",
                                "format": "float",
                                "minimum": 0.01,
                                "maximum": 99.99
                            }
                        }
                    },
                    "manufacturer": {
                        "type": "object",
                        "properties": {
                            "name": {
                                "type": "string",
                                "maxLength": 30
                            },
                            "country": {
                                "type": "string",
                                "enum": [
                                    "CHN",
                                    "USA",
                                    "RUS"
                                ]
                            }
                        }
                    }
                }
            }
        },
        "buyer": {
            "type": "object",
            "properties": {
                "first_name": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 15
                },
                "last_name": {
                    "type": "string",
                    "minLength": 3,
                    "maxLength": 15
                },
                "credit_card": {
                    "type": "integer",
                    "minimum": 1000000000000000,
                    "maximum": 9999999999999999
                },
                "phone": {
                    "type": "integer",
                    "minimum": 10000000000000,
                    "maximum": 99999999999999
                },
                "email": {
                    "type": "string",
                    "format": "email"
                }
            }
        },
        "status": {
            "type": "string",
            "enum": [
                "registered",
                "paid",
                "shipped",
                "delivered"
            ],
            "default": "registered"
        },
        "created_at": {
            "type": "string",
            "format": "date-time"
        }
    }
}
SCHEMA;
}
