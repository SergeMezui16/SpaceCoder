<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Authentication\Entity\UserAuthentication;
use App\Repository\UserRepository;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;

class AuthenticationApiController extends AbstractApiController
{
    public function __construct(
        private UserRepository $users,
        private UrlHelper $urlHelper
    )
    {}

    #[OAT\Post(
        path: '/login_check',
        summary: 'Get JWT Token',
        operationId: 'JWT',
        tags: ['Auth'],
        requestBody: new OAT\RequestBody(
            required: true,
            content: new OAT\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OAT\Property(property: 'username', type: 'string'),
                    new OAT\Property(property: 'password', type: 'string'),
                ]
            )
        ),
        responses: [
            new OAT\Response(
                response: 200,
                description: 'JWT Token',
                content: [
                    new OAT\JsonContent(
                        properties: [
                            new OAT\Property(property: 'token', type: 'string')
                        ]
                    )
                ]
            ),
            new OAT\Response(
                response: 401,
                description: 'Unauthaurized',
                content: [
                    new OAT\JsonContent(
                        properties: [
                            new OAT\Property(property: 'code', type: 'integer', example: 401),
                            new OAT\Property(property: 'message', type: 'string', example: 'Identifiants invalides.')
                        ]
                    )
                ]
            )
        ]
    )]
    public function login()
    {}

    #[OAT\Get(
        path: '/me',
        summary: 'Get connected user',
        operationId: 'me',
        tags: ['Auth'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'An User details',
                content: [
                    new OAT\JsonContent(ref: '#/components/schemas/User')
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
            new OAT\Response(
                response: 401,
                ref: '#/components/responses/ExpiredToken'
            )
        ],
        security: ['Bearer']        
    )]
    #[Route('/me', name: 'api_get_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var UserAuthentication $auth */
        $auth = $this->getUser();

        return $this->json(
            $auth->getUser(),
            Response::HTTP_OK,
            context: ['group' => 'item']
        );
    }

}
