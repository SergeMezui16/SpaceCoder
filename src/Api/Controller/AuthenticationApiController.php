<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Authentication\Entity\UserAuthentication;
use App\Repository\UserRepository;
use OpenApi\Annotations as OA;
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

    /**
     * @OA\Post(
     *     path="/login_check",
     *     summary="Get JWT Token",
     *     operationId="JWT",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username", "password"},
     *              @OA\Property(property="username", type="string"),
     *              @OA\Property(property="password", type="string")
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="JWT Token",
     *         @OA\JsonContent(
     *              @OA\Property(property="token", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Unauthaurized",
     *          @OA\JsonContent(
     *              @OA\Property(property="code", type="integer", example="401"),
     *              @OA\Property(property="message", type="string", example="Identifiants invalides."),
     *          )
     *     )
     * )
     */
    public function login()
    {}

    /**
     * @OA\Get(
     *     path="/me",
     *     summary="Get connected user",
     *     operationId="me",
     *     tags={"Auth"},
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
