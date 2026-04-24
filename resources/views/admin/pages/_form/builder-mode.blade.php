{{-- Builder mode wrapper: Alpine scope + toggle header --}}
<div x-data="{ builderMode: '{{ $editorData->activeBuilder() }}' }"
     x-on:frontend-block-focus.window="builderMode = 'frontend'"
     class="space-y-6">

    @if($editorData->showBuilderToggle())
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-gray-800">Sayfa Düzeni Editörü</h3>
                <p class="mt-1 text-xs text-gray-500">Bu sayfayı eski Blade builder veya yeni Next.js section editor ile düzenleyebilirsin.</p>
            </div>
            <div class="inline-flex rounded-xl bg-gray-100 p-1">
                <button type="button"
                        @click="builderMode = 'frontend'"
                        class="rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="builderMode === 'frontend' ? 'bg-white text-amber-700 shadow-sm' : 'text-gray-600 hover:text-gray-800'">
                    Yeni Builder
                </button>
                <button type="button"
                        @click="builderMode = 'legacy'"
                        class="rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="builderMode === 'legacy' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-600 hover:text-gray-800'">
                    Eski Builder
                </button>
            </div>
        </div>
    </div>
    @endif

    @include('admin.pages._form.legacy-builder')
    @include('admin.pages._form.frontend-editor')

</div>
