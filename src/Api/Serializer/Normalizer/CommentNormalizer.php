<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Comment;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @OA\Schema(
 *      schema="Comments",
 *      description="Comment Collection",
 *      @OA\Property(property="uri", type="string"),
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="author", type="string"),
 *      @OA\Property(property="article", type="string"),
 *      @OA\Property(property="content", type="string"),
 *      @OA\Property(property="replyTo", type="string")
 * )
 * 
 *  * @OA\Schema(
 *      schema="Comment",
 *      description="Comment Item",
 *      allOf={@OA\Schema(ref="#/components/schemas/Comments")},
 *      @OA\Property(property="replies", type="array", @OA\Items(type="string") ),
 *      @OA\Property(property="publishedAt", type="string", format="date-time")
 * )
 */
class CommentNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(
        private UrlGeneratorInterface $url
        
    )
    {}

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Comment) return ['Comment not found.'];

        $data = [];
        $replies = null;
        $group = isset($context['group']) && !empty($context['group']) ? $context['group'] : 'item';
        $uri = $this->url->generate('api_get_comment', ['id' => $object->getId()]);

        $data = [
            'uri' => $uri,
            'id' => $object->getId(),
            'author' => $object->getAuthor() ? $this->url->generate('api_get_user', ['slug' => $object->getAuthor()->getSlug()]) : null,
            'article' => $this->url->generate('api_get_article', ['slug' => $object->getArticle()->getSlug()]),
            'content' => $object->getContent(),
            'replyTo' => $object->getReplyTo() ? $this->url->generate('api_get_comment', ['id' => $object->getReplyTo()->getId()]) : null,
        ];

        foreach ($object->getReplies() as $reply) $replies[] = $this->url->generate('api_get_comment', ['id' => $reply->getId()]);


        if($group === 'item'){
            $data = [
                ...$data,
                'replies' => $replies,
                'publishedAt' => $object->getCreateAt()
            ];
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Comment;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
