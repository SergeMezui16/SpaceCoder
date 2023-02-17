<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Project;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @OA\Schema(
 *      schema="Project",
 *      description="Project",
 *      @OA\Property(property="uri", type="string"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="slug", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(property="views", type="integer"),
 *      @OA\Property(property="url", type="string"),
 *      @OA\Property(property="image", type="string"),
 *      @OA\Property(property="authors", type="string"),
 *      @OA\Property(property="role", type="string"),
 *      @OA\Property(property="createAt", type="string", format="date-time")
 * )
 */
class ProjectNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private UrlGeneratorInterface $url
    ) {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Project) return ['Project not found.'];

        return [
            'uri' => $this->url->generate('api_get_project', ['slug' => $object->getSlug()]),
            'id' => $object->getId(),
            'name' => $object->getName(),
            'slug' => $object->getSlug(),
            'description' => $object->getDescription(),
            'views' => $object->getVisit(),
            'url' => $object->getUrl(),
            'image' => $object->getImage(),
            'authors' => $object->getAuthors(),
            'role' => $object->getRole()->__toString(),
            'createAt' => $object->getCreateAt(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Project;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
