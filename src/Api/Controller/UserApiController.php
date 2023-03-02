<?php
namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\UserRepository;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends AbstractApiController
{
    public function __construct(
        private UserRepository $users,
        private UrlHelper $urlHelper
    )
    {}

    #[OAT\Get(
        path: '/users/{slug}',
        summary: 'Info for a specific user',
        operationId: 'user',
        tags: ['User'],
        parameters: [new OAT\Parameter(ref: '#/components/parameters/slug')],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'An User details',
                content: [
                    new OAT\JsonContent(
                        ref: '#/components/schemas/User'
                    )
                ]
            ),
            new OAT\Response(
                response: 404,
                ref: '#/components/responses/NotFound'
            ),
            new OAT\Response(
                response: 401,
                ref: '#/components/responses/ExpiredToken'
            ),
        ],
        security: ['Bearer']
    )]
    #[Route('/users/{slug}', name: 'api_get_user', methods: ['GET'])]
    public function user(Request $request): JsonResponse
    {
        $user = $this->users->findOneForApi($request->get('slug'));

        if($user === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $user,
            200,
            context: ['group' => 'item']
        );
    }

    #[OAT\Get(
        path: '/users',
        summary: 'List all users',
        operationId: 'users',
        tags: ['User'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'List of Users',
                content: [
                    new OAT\JsonContent(
                        type: 'array',
                        items: new OAT\Items(ref: '#/components/schemas/Users')
                    )
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
    #[Route('/users', name: 'api_get_users', methods: ['GET'])]
    public function users(): JsonResponse
    {
        return $this->json(
            $this->users->findAllForApi(),
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }

    #[OAT\Get(
        path: '/avatars/{slug}',
        summary: 'Get Avatar for an user',
        operationId: 'avatar',
        tags: ['User'],
        parameters: [
            new OAT\Parameter(
                name: 'slug',
                in: 'path',
                description: 'User\'s slug',
                required: true,
                schema: new OAT\Schema(type: 'string')
            )
        ],
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
    #[Route('/avatars/{slug}', name:'api_get_avatar', methods: ['GET'])]
    public function avatar(Request $request): JsonResponse
    {
        $user = $this->users->findOneForApi($request->get('slug'));

        if ($user === null) return $this->json(
            [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'User not found.'
            ],
            Response::HTTP_NOT_FOUND
        );

        return $this->json(
            $user->getAvatar() ? $this->urlHelper->getAbsoluteUrl($user->getAvatar()) : null,
            200,
            context: ['group' => 'item']
        );
    }

}
