<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Project;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[OAT\Schema(
    schema: 'Project',
    description: 'Project',
    properties: [
        new OAT\Property(property: 'uri', type: 'string'),
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'name', type: 'string'),
        new OAT\Property(property: 'slug', type: 'string'),
        new OAT\Property(property: 'description', type: 'string'),
        new OAT\Property(property: 'views', type: 'integer'),
        new OAT\Property(property: 'url', type: 'string'),
        new OAT\Property(property: 'image', type: 'string'),
        new OAT\Property(property: 'authors', type: 'string'),
        new OAT\Property(property: 'role', type: 'string'),
        new OAT\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ]
)]
class ProjectNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private UrlHelper $urlHelper,
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
            'image' => $this->urlHelper->getAbsoluteUrl('/data/project/images/' . $object->getImage()),
            'authors' => $object->getAuthors(),
            'role' => $object->getRole()->__toString(),
            'createdAt' => $object->getCreatedAt(),
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
