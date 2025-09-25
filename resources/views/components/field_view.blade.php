<div class="flex border-b border-grey pt-3 pb-2 px-2">
    <div class="flex-1 pr-4"><span class="text-sm">{{ $code }}</span> {{ $label }}</div>
    <div class="w-1/2 font-light">
        <parsed-text text="{{ $slot }}"></parsed-text>
    </div>
</div>
