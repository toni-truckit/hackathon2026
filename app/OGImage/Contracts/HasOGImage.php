<?php

declare(strict_types=1);

namespace App\OGImage\Contracts;

use App\OGImage\OGImageData;

interface HasOGImage
{
    /**
     * The alias registered in config/og-image.php.
     */
    public function ogImageType(): string;

    /**
     * The Blade view rendering this model's card.
     */
    public function ogImageView(): string;

    /**
     * The cover image (absolute or relative URL) used as card artwork.
     */
    public function ogCoverImageUrl(): ?string;

    /**
     * The author's avatar URL, when available.
     */
    public function ogAuthorAvatarUrl(): ?string;

    /**
     * The data rendered into the card.
     */
    public function ogImageData(): OGImageData;

    /**
     * Content hash embedded in the image URL to bust caches.
     */
    public function ogImageFingerprint(): string;

    /**
     * The public URL of the generated image, or null when disabled.
     */
    public function ogImageUrl(): ?string;

    /**
     * The disk-relative path of the generated image.
     */
    public function ogImageRelativePath(): string;
}
