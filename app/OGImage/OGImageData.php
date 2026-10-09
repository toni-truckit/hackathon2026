<?php

declare(strict_types=1);

namespace App\OGImage;

final readonly class OGImageData
{
    /**
     * @param  array<int, string>  $tags
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?string $eyebrow = null,
        public ?string $authorName = null,
        public ?string $authorAvatarUrl = null,
        public ?string $coverUrl = null,
        public ?string $url = null,
        public ?string $publishedAt = null,
        public array $tags = [],
        public ?string $brandName = null,
        public ?string $brandLogoUrl = null,
        public ?string $domain = null,
        public array $extra = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'eyebrow' => $this->eyebrow,
            'author_name' => $this->authorName,
            'author_avatar_url' => $this->authorAvatarUrl,
            'cover_url' => $this->coverUrl,
            'url' => $this->url,
            'published_at' => $this->publishedAt,
            'tags' => array_values($this->tags),
            'brand_name' => $this->brandName,
            'brand_logo_url' => $this->brandLogoUrl,
            'domain' => $this->domain,
            'extra' => $this->extra,
        ];
    }

    public function fingerprint(): string
    {
        return mb_substr(md5((string) json_encode($this->toArray())), 0, 8);
    }
}
