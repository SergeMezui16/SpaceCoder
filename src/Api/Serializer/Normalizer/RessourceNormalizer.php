<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Ressource;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @OA\Schema(
 *      schema="Ressource",
 *      description="Ressource",
 *      @OA\Property(property="uri", type="string"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="slug", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(property="image", type="string"),
 *      @OA\Property(property="views", type="integer"),
 *      @OA\Property(property="link", type="string"),
 *      @OA\Property(property="cotegories", type="array", @OA\Items(type="string") ),
 *      @OA\Property(property="createAt", type="string", format="date-time")
 * )
 */
class RessourceNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{

    public function __construct(
        private UrlGeneratorInterface $url
    ) {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Ressource) return ['Ressource not found.'];

        return [
            'uri' => $this->url->generate('api_get_ressource', ['slug' => $object->getSlug()]),
            'id' => $object->getId(),
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'description' => $object->getDescription(),
            'image' => $object->getImage(),
            'views' => $object->getClicks(),
            'link' => $object->getLink(),
            'categories' => $object->getCategories(),
            'createAt' => $object->getCreateAt(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Ressource;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
