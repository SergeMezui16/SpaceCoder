<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Article;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @OA\Schema(
 *      schema="Articles",
 *      description="Article Collection",
 *      @OA\Property(property="uri", type="string"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="slug", type="string"),
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="subject", type="string"),
 *      @OA\Property(property="description", type="string"),
 *      @OA\Property(property="views", type="integer"),
 *      @OA\Property(property="publishedAt", type="string", format="date-time"),
 *      @OA\Property(property="comment", type="integer"),
 * )
 * 
 *  * @OA\Schema(
 *      schema="Article",
 *      description="Article Item",
 *      allOf={@OA\Schema(ref="#/components/schemas/Articles")},
 *      @OA\Property(property="level", type="integer"),
 *      @OA\Property(property="content", type="string"),
 *      @OA\Property(property="author", type="string"),
 *      @OA\Property(property="suggeredBy", type="string"),
 *      @OA\Property(property="createAt", type="string", format="date-time"),
 * )
 */
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
