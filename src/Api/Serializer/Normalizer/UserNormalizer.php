<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\User;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @OA\Schema(
 *      schema="Users",
 *      description="User Collection",
 *      @OA\Property(property="uri", type="string"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="pseudo", type="string"),
 * )
 * 
 *  * @OA\Schema(
 *      schema="User",
 *      description="User Item",
 *      allOf={@OA\Schema(ref="#/components/schemas/Articles")},
 *      @OA\Property(property="slug", type="string"),
 *      @OA\Property(property="coin", type="integer"),
 *      @OA\Property(property="avatar", type="string"),
 *      @OA\Property(property="bio", type="string"),
 *      @OA\Property(property="country", type="string"),
 * )
 */
class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private UrlHelper $urlHelper,
        private UrlGeneratorInterface $url
    )
    {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof User) return ['User not found.'];

        $data = [];
        $group = isset($context['group']) && !empty($context['group']) ? $context['group'] : 'item';
        $uri = $this->url->generate('api_get_user', ['slug' => $object->getSlug()]);

        $data = [
            'uri' => $uri,
            'id' => $object->getId(),
            'pseudo' => $object->getPseudo(),
        ];

        if($group === 'item'){
            $data = [
                ...$data,
                'slug' => $object->getSlug(),
                'coin' => $object->getCoins(),
                'avatar' => $object->getAvatar() ? $this->urlHelper->getAbsoluteUrl($object->getAvatar()) : null,
                'bio' => $object->getBio(),
                'country' => $object->getCountry()
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
