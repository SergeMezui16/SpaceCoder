<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArticleNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{

        
    public function __construct(
        private UrlHelper $urlHelper,
        private UrlGeneratorInterface $url
    )
    {}

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
            'image' => $object->getImage(),
        ];

        if ($group === 'item') {
            $data = [
                ...$data,
                'level' => $object->getLevel(),
                'content' => $object->getContent(),
                'author' => $object->getAuthor() ? $this->url->generate('api_get_user', ['slug' => $object->getAuthor()->getSlug()]) : null,
                'suggestedBy' => $object->getSuggestedBy() ? $this->url->generate('api_get_user', ['slug' => $object->getSuggestedBy()->getSlug()]) : null,
                'createAt' => $object->getCreateAt()
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
