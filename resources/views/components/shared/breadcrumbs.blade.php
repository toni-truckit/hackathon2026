@props(['items' => []])

@if (! empty($items))
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex flex-wrap items-center gap-2 text-xs font-semibold text-neutral-400">
            @foreach ($items as $item)
                <li class="flex items-center gap-2">
                    @if (! $loop->last && filled($item['url'] ?? null))
                        <a href="{{ $item['url'] }}" class="transition-colors hover:text-primary" wire:navigate.hover>
                            {{ $item['label'] }}
                        </a>
                        <span aria-hidden="true">/</span>
                    @else
                        <span @if ($loop->last) aria-current="page" class="text-neutral-600" @endif>
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
