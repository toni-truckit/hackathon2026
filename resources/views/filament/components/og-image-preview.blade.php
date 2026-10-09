@if (filled($previewUrl))
    <div class="space-y-2">
        <img
            src="{{ $previewUrl }}"
            alt="Generated Open Graph image preview"
            class="w-full max-w-xl rounded-lg border border-gray-200 dark:border-white/10"
            loading="lazy"
        >
        <p class="text-xs text-gray-500 dark:text-gray-400">
            The card is generated on first request. Any change to the content, author or cover refreshes it automatically.
        </p>
    </div>
@endif
