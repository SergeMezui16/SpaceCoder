<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
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

    /**
     * @OA\Get(
     *     path="/user/{slug}",
     *     summary="Info for a specific user",
     *     operationId="user",
     *     tags={"User"},
     *     @OA\Parameter(ref="#/components/parameters/slug"),
     *     @OA\Response(
     *         response="200",
     *         description="An User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/ExpiredToken"
     *     ),
     * 
     *     security={"Bearer"}
     * )
     */
    #[Route('/user/{slug}', name: 'api_get_user', methods: ['GET'])]
    public function user(Request $request): JsonResponse
    {
        $user = $this->users->findOneBy(['slug' => $request->get('slug')]);

        if($user === null || $user->getAuth()->isBlocked() === true) return $this->json(
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

    /**
     * @OA\Get(
     *     path="/users",
     *     summary="List all users",
     *     operationId="users",
     *     tags={"User"},
     *     @OA\Response(
     *         response="200",
     *         description="List of Users",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Users") 
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     ),
     * 
     * 
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/ExpiredToken"
     *     ),
     * 
     *     security={"Bearer"}
     * )
     */
    #[Route('/users', name: 'api_get_users', methods: ['GET'])]
    public function users(): JsonResponse
    {
        return $this->json(
            array_values(
                array_filter(
                    $this->users->findAll(), 
                    fn ($user) => $user->getAuth()->isBlocked() === false
                )
            ),
            Response::HTTP_OK,
            context: ['group' => 'collection']
        );
    }

    /**
     * @OA\Get(
     *     path="/avatar/{slug}",
     *     summary="Get Avatar for an user",
     *     operationId="avatar",
     *     tags={"User"},
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="User's slug",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response="200",
     *         description="An User details",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         ref="#/components/responses/NotFound"
     *     ),
     * 
     * 
     *     @OA\Response(
     *         response="401",
     *         ref="#/components/responses/ExpiredToken"
     *     ),
     * 
     *     security={"Bearer"}
     * )
     */
    #[Route('/avatar/{slug}', name:'api_get_avatar', methods: ['GET'])]
    public function avatar(Request $request): JsonResponse
    {
        $user = $this->users->findOneBy(['slug' => $request->get('slug')]);

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
