<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Project;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
