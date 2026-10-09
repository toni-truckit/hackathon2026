<?php

declare(strict_types=1);

use App\OGImage\OGImageData;

it('maps og image data to a stable view payload', function (): void {
    $data = new OGImageData(
        title: 'Dynamic Open Graph Images',
        description: 'How to render social cards on demand.',
        eyebrow: 'Engineering',
        tags: ['laravel', 'seo'],
    );

    expect($data->toArray())
        ->toMatchArray([
            'title' => 'Dynamic Open Graph Images',
            'description' => 'How to render social cards on demand.',
            'eyebrow' => 'Engineering',
            'tags' => ['laravel', 'seo'],
        ])
        ->and($data->fingerprint())->toHaveLength(8);
});

it('produces different fingerprints for different payloads', function (): void {
    $first = new OGImageData(title: 'First');
    $second = new OGImageData(title: 'Second');

    expect($first->fingerprint())->not->toBe($second->fingerprint());
});
