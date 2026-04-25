{{-- Builder mode wrapper: Alpine scope + context-aware toggle --}}
<div x-data="{
    builderMode: '{{ $editorData->activeBuilder() }}',

    {{-- Migrate preview modal state --}}
    migrateModal: false,
    migrateLoading: false,
    migrateError: null,
    currentJson: null,
    previewJson: null,
    migrateUrl: '{{ isset($page) ? route('admin.pages.migrate-to-sections', $page) : '' }}',
    previewUrl: '{{ isset($page) ? route('admin.pages.migrate-preview', $page) : '' }}',

    async openMigrateModal() {
        this.migrateModal   = true;
        this.migrateLoading = true;
        this.migrateError   = null;
        this.currentJson    = null;
        this.previewJson    = null;
        try {
            const res = await fetch(this.previewUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                this.migrateError = err.error || 'Önizleme yüklenemedi.';
            } else {
                const data = await res.json();
                this.currentJson = JSON.stringify(data.current_sections ?? {}, null, 2);
                this.previewJson = JSON.stringify(data.preview_sections ?? {}, null, 2);
            }
        } catch (e) {
            this.migrateError = 'Sunucuya ulaşılamadı.';
        } finally {
            this.migrateLoading = false;
        }
    },

    closeMigrateModal() {
        this.migrateModal = false;
    },
}"
     x-on:frontend-block-focus.window="builderMode = 'frontend'"
     x-on:keydown.escape.window="closeMigrateModal()"
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
                    {{-- Migrate CTA — only when legacy-only; opens preview modal first --}}
                    <button type="button"
                            @click="openMigrateModal()"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        Yeni Builder'a Dönüştür
                    </button>
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

    {{-- ─── Migrate Preview Modal ──────────────────────────────────────── --}}
    @if($editorData->hasLayoutJson() && !$editorData->hasSectionsJson())
    <template x-teleport="body">
        <div x-show="migrateModal"
             x-cloak
             class="fixed inset-0 z-[60] flex items-start justify-center overflow-y-auto p-4 sm:p-8"
             style="background: rgba(0,0,0,0.55);"
             @click.self="closeMigrateModal()">

            <div class="relative w-full max-w-5xl rounded-2xl bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            <i class="fas fa-wand-magic-sparkles mr-2 text-amber-500"></i>
                            Yeni Builder'a Dönüştür — Önizleme
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Dönüşüm sonrası <code class="rounded bg-gray-100 px-1 font-mono">sections_json</code>'un nasıl görüneceğini incele, ardından onayla.
                        </p>
                    </div>
                    <button type="button" @click="closeMigrateModal()"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6">

                    {{-- Loading --}}
                    <div x-show="migrateLoading" class="flex items-center justify-center py-16 text-sm text-gray-500">
                        <i class="fas fa-circle-notch fa-spin mr-2 text-amber-500"></i>
                        Dönüşüm önizlemesi hazırlanıyor…
                    </div>

                    {{-- Error --}}
                    <div x-show="migrateError && !migrateLoading"
                         class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                         x-text="migrateError"></div>

                    {{-- Side-by-side diff --}}
                    <div x-show="!migrateLoading && !migrateError" class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        {{-- Current (left) --}}
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                                    Şu an (legacy)
                                </span>
                                <span class="text-xs text-gray-400">sections_json</span>
                            </div>
                            <pre class="h-80 overflow-auto rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700 leading-relaxed"
                                 x-text="currentJson ?? '—'"></pre>
                        </div>

                        {{-- Preview (right) --}}
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                    Dönüştürülecek (yeni)
                                </span>
                                <span class="text-xs text-gray-400">sections_json</span>
                            </div>
                            <pre class="h-80 overflow-auto rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900 leading-relaxed"
                                 x-text="previewJson ?? '—'"></pre>
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div x-show="!migrateLoading && !migrateError"
                         class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        <i class="fas fa-triangle-exclamation mr-1"></i>
                        Bu işlem <strong>geri alınabilir</strong> — dönüşüm öncesi otomatik bir revision kaydedilir.
                        Geri yüklemek için sayfa düzenleme ekranının sidebar'ındaki Revizyon Geçmişi'ni kullan.
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <button type="button" @click="closeMigrateModal()"
                            class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        İptal
                    </button>

                    {{-- Real POST form, triggered from modal --}}
                    <form method="POST" :action="migrateUrl" x-ref="migrateForm">
                        @csrf
                        <button type="submit"
                                x-show="!migrateLoading && !migrateError"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1">
                            <i class="fas fa-wand-magic-sparkles"></i>
                            Onayla ve Dönüştür
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
    @endif

</div>
