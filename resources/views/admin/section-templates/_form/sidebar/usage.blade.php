<div class="rounded-xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
    <div class="flex items-center justify-between gap-3">
        <h3 class="text-base font-semibold text-sky-900">Bu Şablonu Kullanan Sayfalar</h3>
        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sky-700">{{ count($usagePages ?? []) }}</span>
    </div>

    @if(!empty($usagePages))
        <div class="mt-4 space-y-2">
            @foreach($usagePages as $usedPage)
                <a href="{{ route('admin.pages.edit', $usedPage['id']) }}"
                   class="flex items-center justify-between gap-3 rounded-lg border border-sky-100 bg-white px-3 py-2 text-sm text-sky-900 hover:bg-sky-100">
                    <span class="min-w-0 truncate">
                        <i class="fas fa-file-lines mr-1.5 text-sky-500"></i>{{ $usedPage['title'] }}
                    </span>
                    <span class="shrink-0 font-mono text-xs text-sky-500">/{{ $usedPage['slug'] }}</span>
                </a>
            @endforeach
        </div>
        <p class="mt-3 text-xs text-sky-800">
            Bu şablonda yapacağın değişiklikler yukarıdaki sayfalardaki block render sonucunu etkileyebilir.
        </p>
    @else
        <p class="mt-3 text-sm text-sky-800">Bu şablon henüz hiçbir sayfada kullanılmıyor.</p>
    @endif
</div>
