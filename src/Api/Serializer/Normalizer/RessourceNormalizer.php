<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Ressource;
use OpenApi\Attributes as OAT;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OAT\Schema(
    schema: 'Ressource',
    description: 'Ressource',
    properties: [
        new OAT\Property(property: 'uri', type: 'string'),
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'name', type: 'string'),
        new OAT\Property(property: 'slug', type: 'string'),
        new OAT\Property(property: 'description', type: 'string'),
        new OAT\Property(property: 'image', type: 'string'),
        new OAT\Property(property: 'views', type: 'integer'),
        new OAT\Property(property: 'link', type: 'string'),
        new OAT\Property(property: 'cotegories', type: 'array', items: new OAT\Items(type: 'string')),
        new OAT\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ]
)]
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
            'createdAt' => $object->getCreatedAt(),
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
