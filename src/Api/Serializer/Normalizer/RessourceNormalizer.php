<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Ressource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
