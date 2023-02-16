<?php

namespace App\Api\Serializer\Normalizer;

use App\Entity\Comment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
