{{-- Builder mode wrapper: Alpine scope + context-aware toggle --}}
<div x-data="{ builderMode: '{{ $editorData->activeBuilder() }}' }"
     x-on:frontend-block-focus.window="builderMode = 'frontend'"
     class="space-y-6">

    @if($editorData->showBuilderToggle())
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">

            {{-- Context-aware label --}}
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-800">Sayfa Düzeni Editörü</h3>

                @if($editorData->hasLayoutJson() && !$editorData->hasSectionsJson())
                    {{-- Legacy only → encourage migration --}}
                    <p class="mt-1 text-xs text-amber-700">
                        <i class="fas fa-triangle-exclamation mr-1"></i>
                        Bu sayfa <strong>eski builder</strong> ile oluşturulmuş.
                        Next.js frontend için <strong>Yeni Builder'a Dönüştür</strong>'ü kullan.
                    </p>
                @elseif($editorData->hasLayoutJson() && $editorData->hasSectionsJson())
                    {{-- Both present → warn about conflict --}}
                    <p class="mt-1 text-xs text-indigo-700">
                        <i class="fas fa-circle-info mr-1"></i>
                        Her iki builder'da içerik mevcut.
                        Next.js frontend yalnızca <code class="rounded bg-indigo-50 px-1 py-0.5 font-mono text-indigo-700">sections_json</code>'u kullanır.
                    </p>
                @else
                    {{-- Orphan page (no site) --}}
                    <p class="mt-1 text-xs text-gray-500">
                        Eski Blade builder veya yeni Next.js section editor ile düzenle.
                    </p>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2">

                @if($editorData->hasLayoutJson() && !$editorData->hasSectionsJson())
                    {{-- Migrate CTA — only when legacy-only --}}
                    <form method="POST" action="{{ route('admin.pages.migrate-to-sections', $page) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Bu sayfa eski builder formatında. Yeni builder\'a dönüştürülecek — onaylıyor musun?')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            Yeni Builder'a Dönüştür
                        </button>
                    </form>
                @endif

                {{-- Builder toggle --}}
                <div class="inline-flex rounded-xl bg-gray-100 p-1">
                    <button type="button"
                            @click="builderMode = 'frontend'"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                            :class="builderMode === 'frontend'
                                ? 'bg-white text-amber-700 shadow-sm'
                                : 'text-gray-500 hover:text-gray-800'">
                        <i class="fas fa-layer-group mr-1"></i>Yeni
                    </button>
                    <button type="button"
                            @click="builderMode = 'legacy'"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                            :class="builderMode === 'legacy'
                                ? 'bg-white text-indigo-700 shadow-sm'
                                : 'text-gray-500 hover:text-gray-800'">
                        <i class="fas fa-th-large mr-1"></i>Eski
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('admin.pages._form.legacy-builder')
    @include('admin.pages._form.frontend-editor')

</div>
