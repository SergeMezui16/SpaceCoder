<?php
namespace App\Api\Controller;

use OpenApi\Attributes as OAT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OAT\Parameter(
    name: 'slug',
    in: 'path',
    description: 'Ressource slug',
    required: true,
    schema: new OAT\Schema(type: 'string')
)]
#[OAT\Response(
    response: 'NotFound',
    description: 'Ressource not found.',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(property: 'code', type: 'integer', example: 404),
            new OAT\Property(property: 'message', type: 'string', example: 'Ressource not found.')
        ]
    )
)]
#[OAT\Response(
    response: 'ExpiredToken',
    description: 'Unauthaurized',
    content: new OAT\JsonContent(
        properties: [
            new OAT\Property(property: 'code', type: 'integer', example: 401),
            new OAT\Property(property: 'message', type: 'string', example: 'Expired JWT Token')
        ]
    )
)]
#[OAT\SecurityScheme(
    securityScheme: 'Bearer',
    type: 'http',
    scheme: 'bearer',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'JWT',
    description: '>- Enter the token with the `Bearer: {token}`, e.g. Bearer abcde12345'
)]
class AbstractApiController extends AbstractController
{
}