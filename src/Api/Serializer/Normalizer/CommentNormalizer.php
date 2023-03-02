<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Comment;
use OpenApi\Attributes as OAT;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


#[OAT\Schema(
    schema: 'Comments',
    description: 'Comment Collection',
    properties: [
        new OAT\Property(property: 'uri', type: 'string'),
        new OAT\Property(property: 'id', type: 'integer'),
        new OAT\Property(property: 'author', type: 'string'),
        new OAT\Property(property: 'article', type: 'string'),
        new OAT\Property(property: 'content', type: 'string'),
        new OAT\Property(property: 'replyTo', type: 'string')
    ]
)]
#[OAT\Schema(
    schema: 'Comment',
    description: 'Comment Item',
    allOf: [new OAT\Schema(ref: '#/components/schemas/Comments')],
    properties: [
        new OAT\Property(property: 'replies', type: 'array', items: new OAT\Items(type: 'string')),
        new OAT\Property(property: 'publishedAt', type: 'string', format: 'date-time')
    ]
)]
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
