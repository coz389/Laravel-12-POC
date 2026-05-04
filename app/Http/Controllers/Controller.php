<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "My Laravel 12 API Documentation",
    version: "1.0.0",
    description: "API documentation for my new Laravel application",
    contact: new OA\Contact(email: "admin@example.com"),
    license: new OA\License(name: "Apache 2.0", url: "http://apache.org")
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Primary API Server"
)]
#[OA\Schema(
    schema: "PaginatedResponse",
    properties: [
        new OA\Property(property: "status", type: "boolean", example: true),
        new OA\Property(property: "message", type: "string"),
        new OA\Property(
            property: "data",
            type: "object",
            properties: [
                new OA\Property(property: "current_page", type: "integer", example: 1),
                new OA\Property(property: "last_page", type: "integer", example: 10),
                new OA\Property(property: "total", type: "integer", example: 100),
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(type: "object")
                )
            ]
        )
    ]
)]
abstract class Controller
{
    //
}
