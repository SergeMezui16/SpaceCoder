<?php

namespace App\Api\Controller;

use App\Api\Controller\AbstractApiController;
use App\Authentication\Entity\UserAuthentication;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserApiController extends AbstractApiController
{
    public function __construct(
        private UserRepository $users,
        private UrlHelper $urlHelper
    )
    {}
    
    // Get User
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

    // Get Me
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

    // Get user avatar
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
