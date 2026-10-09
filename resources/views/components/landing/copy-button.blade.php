@props([
    'text',
    'label' => 'Copy',
    'copiedLabel' => 'Copied',
])

<button
    type="button"
    {{ $attributes->merge(['class' => 'landing-btn-ghost shrink-0']) }}
    x-data="{ copied: false }"
    x-on:click="
        navigator.clipboard.writeText(@js($text));
        copied = true;
        setTimeout(() => copied = false, 2000);
    "
>
    <span x-show="!copied" x-cloak>{{ $label }}</span>
    <span x-show="copied" x-cloak>{{ $copiedLabel }}</span>
</button>
