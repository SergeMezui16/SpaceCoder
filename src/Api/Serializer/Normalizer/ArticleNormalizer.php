<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Article;
use OpenApi\Attributes as OAT;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[OAT\Schema(
    schema: 'Articles',
    description: 'Article Collection',
    properties: [
        new OAT\Property(property: 'uri', type: 'string'),
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'slug', type: 'string'),
        new OAT\Property(property: 'title', type: 'string'),
        new OAT\Property(property: 'subject', type: 'string'),
        new OAT\Property(property: 'description', type: 'string'),
        new OAT\Property(property: 'views', type: 'integer'),
        new OAT\Property(property: 'publishedAt', type: 'string', format: 'date-time'),
        new OAT\Property(property: 'comment', type: 'integer')
    ]
)]
#[OAT\Schema(
    schema: 'Article',
    description: 'Article Item',
    allOf: [new OAT\Schema(ref: '#/components/schemas/Articles')],
    properties: [
        new OAT\Property(property: 'level', type: 'integer'),
        new OAT\Property(property: 'content', type: 'string'),
        new OAT\Property(property: 'author', type: 'string'),
        new OAT\Property(property: 'suggeredBy', type: 'string'),
        new OAT\Property(property: 'createdAt', type: 'string', format: 'date-time')
    ]
)]
class ArticleNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private UrlHelper $urlHelper,
        private UrlGeneratorInterface $url
    ) {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Article) return ['Article not found.'];

        $data = [];
        $group = isset($context['group']) && !empty($context['group']) ? $context['group'] : 'item';
        $uri = $this->url->generate('api_get_article', ['slug' => $object->getSlug()]);

        $data = [
            'uri' => $uri,
            'id' => $object->getId(),
            'slug' => $object->getSlug(),
            'title' => $object->getTitle(),
            'subject' => $object->getSubject(),
            'description' => $object->getDescription(),
            'views' => $object->getViews(),
            'publishedAt' => $object->getPublishedAt(),
            'comment' => $object->getComments()->count(),
            'image' => $this->urlHelper->getAbsoluteUrl('/data/article/images/' . $object->getImage())
        ];

        if ($group === 'item') {
            $data = [
                ...$data,
                'level' => $object->getLevel(),
                'content' => $object->getContent(),
                'author' => $object->getAuthor() ? $this->url->generate('api_get_user', ['slug' => $object->getAuthor()->getSlug()]) : null,
                'suggestedBy' => $object->getSuggestedBy() ? $this->url->generate('api_get_user', ['slug' => $object->getSuggestedBy()->getSlug()]) : null,
                'createdAt' => $object->getCreatedAt()
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Article;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
