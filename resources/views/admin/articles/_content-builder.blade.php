{{--
    Article Content Builder
    ─────────────────────────────────────────────────────────────────────
    Block types: heading | paragraph | image | video | html
    Each block has: id (unique), type, type-specific fields, collapsed (UI only)
    On save: hidden input "content_json" carries JSON → controller renders body HTML
--}}
@php
    // If content_json is empty but body exists, auto-wrap body as a single html block
    // so existing articles open correctly in the block editor.
    $initialBlocks = $article->content_json ?? [];
    if (empty($initialBlocks) && !empty($article->body ?? '')) {
        $initialBlocks = [[
            'id'   => 'legacy_body',
            'type' => 'html',
            'code' => $article->body,
        ]];
    }
@endphp

<div x-data="articleBlockEditor({{ Js::from($initialBlocks) }})"
     x-on:keydown.escape.window="closeMediaPicker()"
     class="space-y-3">

    {{-- Hidden input synced before submit --}}
    <input type="hidden" name="content_json" x-bind:value="getJson()">

    {{-- ─── Block list ──────────────────────────────────────────────── --}}
    <div class="space-y-3">
        <template x-for="(block, index) in blocks" :key="block.id">
            <div class="rounded-xl border bg-white shadow-sm overflow-hidden"
                 :class="{
                     'border-indigo-200': block.type === 'heading',
                     'border-gray-200':   block.type === 'paragraph',
                     'border-green-200':  block.type === 'image',
                     'border-red-200':    block.type === 'video',
                     'border-amber-200':  block.type === 'html',
                 }">

                {{-- Block header --}}
                <div class="flex items-center gap-2 px-4 py-2.5 border-b"
                     :class="{
                         'border-indigo-100 bg-indigo-50': block.type === 'heading',
                         'border-gray-100   bg-gray-50':   block.type === 'paragraph',
                         'border-green-100  bg-green-50':  block.type === 'image',
                         'border-red-100    bg-red-50':    block.type === 'video',
                         'border-amber-100  bg-amber-50':  block.type === 'html',
                     }">

                    {{-- Type badge --}}
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-full px-2.5 py-0.5"
                          :class="{
                              'bg-indigo-100 text-indigo-700': block.type === 'heading',
                              'bg-gray-200   text-gray-700':   block.type === 'paragraph',
                              'bg-green-100  text-green-700':  block.type === 'image',
                              'bg-red-100    text-red-700':    block.type === 'video',
                              'bg-amber-100  text-amber-700':  block.type === 'html',
                          }">
                        <i class="fas"
                           :class="{
                               'fa-heading':   block.type === 'heading',
                               'fa-paragraph': block.type === 'paragraph',
                               'fa-image':     block.type === 'image',
                               'fa-play':      block.type === 'video',
                               'fa-code':      block.type === 'html',
                           }"></i>
                        <span x-text="{
                            heading:   'Başlık',
                            paragraph: 'Metin',
                            image:     'Görsel',
                            video:     'Video',
                            html:      'HTML',
                        }[block.type] || block.type"></span>
                        {{-- Preview snippet for collapsed state --}}
                        <span x-show="block.collapsed" class="font-normal text-[10px] opacity-60 truncate max-w-[200px]"
                              x-text="previewText(block)"></span>
                    </span>

                    <div class="flex-1"></div>

                    {{-- Reorder --}}
                    <button type="button" @@click="moveUp(index)" :disabled="index === 0"
                            class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" title="Yukarı taşı">
                        <i class="fas fa-chevron-up text-xs"></i>
                    </button>
                    <button type="button" @@click="moveDown(index)" :disabled="index === blocks.length - 1"
                            class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed" title="Aşağı taşı">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    {{-- Collapse --}}
                    <button type="button" @@click="block.collapsed = !block.collapsed"
                            class="p-1 text-gray-400 hover:text-gray-700" :title="block.collapsed ? 'Aç' : 'Daralt'">
                        <i class="fas text-xs" :class="block.collapsed ? 'fa-expand' : 'fa-compress'"></i>
                    </button>

                    {{-- Delete --}}
                    <button type="button" @@click="removeBlock(index)"
                            class="p-1 text-gray-400 hover:text-red-600 ml-1" title="Bloğu sil">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>

                {{-- ─── Block Content ──────────────────────────────────── --}}
                <div x-show="!block.collapsed" class="p-4">

                    {{-- ── HEADING ──────────────────────────────────────── --}}
                    <div x-show="block.type === 'heading'" class="flex items-center gap-3">
                        <select x-model="block.level"
                                class="w-20 rounded-lg border border-gray-300 px-2 py-2 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="1">H1</option>
                            <option value="2">H2</option>
                            <option value="3">H3</option>
                            <option value="4">H4</option>
                            <option value="5">H5</option>
                            <option value="6">H6</option>
                        </select>
                        <input type="text" x-model="block.text"
                               placeholder="Başlık metnini girin…"
                               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :style="{ fontSize: { 1:'1.5rem',2:'1.25rem',3:'1.1rem',4:'1rem',5:'.9rem',6:'.8rem' }[block.level] || '1rem' }">
                    </div>

                    {{-- ── PARAGRAPH (Quill) ───────────────────────────── --}}
                    <div x-show="block.type === 'paragraph'"
                         x-init="block.type === 'paragraph' && $nextTick(() => {
                             if ($el.querySelector('.ql-editor')) return;
                             if (typeof Quill === 'undefined') return;
                             const q = new Quill($el.querySelector('[data-quill]'), {
                                 theme: 'snow',
                                 modules: { toolbar: [
                                     [{ header: [2,3,4,false] }],
                                     ['bold','italic','underline','strike'],
                                     [{ list:'ordered'},{ list:'bullet' }],
                                     ['link','clean'],
                                 ]},
                             });
                             if (block.content) q.clipboard.dangerouslyPasteHTML(block.content);
                             q.on('text-change', () => { block.content = q.root.innerHTML; });
                         })">
                        <div data-quill style="min-height:120px"></div>
                    </div>

                    {{-- ── IMAGE ────────────────────────────────────────── --}}
                    <div x-show="block.type === 'image'" class="space-y-3">
                        {{-- URL + media picker --}}
                        <div class="flex gap-2">
                            <input type="text" x-model="block.url"
                                   placeholder="https://… ya da medya kütüphanesinden seç"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <button type="button" @@click="openMediaPicker(block)"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-100 border border-green-200">
                                <i class="fas fa-folder-open"></i> Medya
                            </button>
                        </div>
                        {{-- Preview --}}
                        <div x-show="block.url" class="relative">
                            <img :src="block.url" @@error="$el.style.display='none'" @@load="$el.style.display='block'"
                                 class="max-h-48 rounded-lg border border-gray-200 object-cover" style="display:none" alt="">
                        </div>
                        {{-- Alt + Caption --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500">Alt metni</label>
                                <input type="text" x-model="block.alt"
                                       placeholder="Erişilebilirlik metni"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-500">Açıklama (caption)</label>
                                <input type="text" x-model="block.caption"
                                       placeholder="Görsel altına yazı"
                                       class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    {{-- ── VIDEO ────────────────────────────────────────── --}}
                    <div x-show="block.type === 'video'" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" x-model="block.url"
                                   @@input="parseVideoUrl(block)"
                                   placeholder="YouTube veya Vimeo URL'si"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent">
                            <span x-show="block.provider"
                                  class="inline-flex items-center gap-1 rounded-lg bg-red-50 border border-red-200 px-3 text-xs font-medium text-red-700"
                                  x-text="block.provider === 'youtube' ? 'YouTube' : 'Vimeo'"></span>
                        </div>
                        {{-- Embed preview --}}
                        <div x-show="block.embed_url" class="rounded-lg overflow-hidden border border-gray-200 bg-black"
                             style="position:relative;padding-bottom:56.25%;height:0">
                            <iframe :src="block.embed_url" frameborder="0" allowfullscreen
                                    style="position:absolute;top:0;left:0;width:100%;height:100%"></iframe>
                        </div>
                        {{-- Caption --}}
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Açıklama</label>
                            <input type="text" x-model="block.caption"
                                   placeholder="Video açıklaması (isteğe bağlı)"
                                   class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent">
                        </div>
                    </div>

                    {{-- ── HTML CODE ────────────────────────────────────── --}}
                    <div x-show="block.type === 'html'">
                        <textarea x-model="block.code" rows="8"
                                  placeholder="<div>HTML kodu buraya…</div>"
                                  class="w-full rounded-lg border border-gray-300 bg-gray-950 px-3 py-2 text-xs text-green-400 font-mono focus:ring-2 focus:ring-amber-400 focus:border-transparent resize-y leading-relaxed"></textarea>
                    </div>

                </div>{{-- /block content --}}
            </div>{{-- /block card --}}
        </template>
    </div>{{-- /block list --}}

    {{-- ─── Empty state ─────────────────────────────────────────────── --}}
    <div x-show="blocks.length === 0"
         class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 py-16 text-gray-400">
        <i class="fas fa-layer-group text-3xl mb-3"></i>
        <p class="text-sm font-medium">Henüz blok eklenmedi</p>
        <p class="mt-1 text-xs">Aşağıdaki düğmelerden içerik bloğu ekleyin</p>
    </div>

    {{-- ─── Add Block Picker ────────────────────────────────────────── --}}
    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4">
        <p class="mb-3 text-xs font-medium text-gray-500 uppercase tracking-wide">Blok Ekle</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="bt in blockTypes" :key="bt.type">
                <button type="button" @@click="addBlock(bt.type)"
                        class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-xs font-medium transition-colors hover:shadow-sm"
                        :class="{
                            'border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100': bt.type === 'heading',
                            'border-gray-200   bg-white   text-gray-700   hover:bg-gray-100':   bt.type === 'paragraph',
                            'border-green-200  bg-green-50 text-green-700  hover:bg-green-100':  bt.type === 'image',
                            'border-red-200    bg-red-50   text-red-700    hover:bg-red-100':    bt.type === 'video',
                            'border-amber-200  bg-amber-50 text-amber-700  hover:bg-amber-100':  bt.type === 'html',
                        }">
                    <i class="fas" :class="bt.icon"></i>
                    <span x-text="bt.label"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- ─── Media Picker Modal ──────────────────────────────────────── --}}
    <template x-teleport="body">
        <div x-show="mediaPickerOpen" x-cloak
             class="fixed inset-0 z-[70] flex items-start justify-center overflow-y-auto p-4 sm:p-8"
             style="background:rgba(0,0,0,0.55);"
             @@click.self="closeMediaPicker()">

            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-images mr-2 text-green-500"></i>Medya Kütüphanesi
                    </h2>
                    <button type="button" @@click="closeMediaPicker()"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6">
                    <input type="text" x-model="mediaSearch"
                           placeholder="Görsellerde ara…"
                           class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent">

                    <div x-show="mediaLoading" class="flex justify-center py-12 text-gray-400">
                        <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                    </div>

                    <div x-show="!mediaLoading"
                         class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-96 overflow-y-auto">
                        <template x-for="item in filteredMedia()" :key="item.id">
                            <button type="button" @@click="selectMedia(item)"
                                    class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 hover:border-green-400 hover:shadow-md transition-all">
                                <img :src="item.thumbnail_url || item.url" :alt="item.name"
                                     class="h-full w-full object-cover" loading="lazy">
                                <div class="absolute inset-0 flex items-center justify-center bg-green-500/80 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-check text-white text-lg"></i>
                                </div>
                            </button>
                        </template>
                        <div x-show="filteredMedia().length === 0 && !mediaLoading"
                             class="col-span-full py-8 text-center text-sm text-gray-400">
                            Görsel bulunamadı.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

</div>{{-- /x-data --}}

@push('scripts')
<script>
function articleBlockEditor(initialBlocks) {
    return {
        blocks: (initialBlocks || []).map(b => ({
            collapsed: false,
            mediaPickerOpen: false,
            provider: b.provider || '',
            ...b,
        })),

        // Media picker state (shared across all image blocks)
        mediaPickerOpen:  false,
        mediaPickerBlock: null,
        mediaItems:       [],
        mediaSearch:      '',
        mediaLoading:     false,

        blockTypes: [
            { type: 'heading',   label: 'Başlık',   icon: 'fa-heading'   },
            { type: 'paragraph', label: 'Metin',    icon: 'fa-paragraph' },
            { type: 'image',     label: 'Görsel',   icon: 'fa-image'     },
            { type: 'video',     label: 'Video',    icon: 'fa-play'      },
            { type: 'html',      label: 'HTML',     icon: 'fa-code'      },
        ],

        uid() {
            return 'blk_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7);
        },

        addBlock(type) {
            const id      = this.uid();
            const base    = { id, type, collapsed: false };
            const defs    = {
                heading:   { level: 2, text: '' },
                paragraph: { content: '' },
                image:     { url: '', alt: '', caption: '' },
                video:     { url: '', embed_url: '', provider: '', caption: '' },
                html:      { code: '' },
            };
            this.blocks.push({ ...base, ...(defs[type] || {}) });

            // Scroll new block into view
            this.$nextTick(() => {
                const cards = this.$el.querySelectorAll('[x-for]');
                const last  = this.$el.querySelector('.space-y-3 > div:last-child');
                if (last) last.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        },

        removeBlock(index) {
            if (confirm('Bu bloğu silmek istiyor musunuz?')) {
                this.blocks.splice(index, 1);
            }
        },

        moveUp(index) {
            if (index > 0) {
                this.blocks.splice(index - 1, 2, this.blocks[index], this.blocks[index - 1]);
            }
        },

        moveDown(index) {
            if (index < this.blocks.length - 1) {
                this.blocks.splice(index, 2, this.blocks[index + 1], this.blocks[index]);
            }
        },

        parseVideoUrl(block) {
            const url = block.url || '';
            const yt  = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&?\/\s]+)/);
            if (yt)  { block.embed_url = 'https://www.youtube.com/embed/' + yt[1];     block.provider = 'youtube'; return; }
            const vi = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
            if (vi)  { block.embed_url = 'https://player.vimeo.com/video/' + vi[1]; block.provider = 'vimeo';   return; }
            block.embed_url = '';
            block.provider  = '';
        },

        // Media picker
        openMediaPicker(block) {
            this.mediaPickerBlock = block;
            this.mediaPickerOpen  = true;
            if (!this.mediaItems.length) this.loadMedia();
        },
        closeMediaPicker() {
            this.mediaPickerOpen  = false;
            this.mediaPickerBlock = null;
        },
        async loadMedia() {
            this.mediaLoading = true;
            try {
                const res  = await fetch('/admin/media?type=image&per_page=96', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                this.mediaItems = data.data || [];
            } catch { this.mediaItems = []; }
            finally  { this.mediaLoading = false; }
        },
        filteredMedia() {
            const q = this.mediaSearch.toLowerCase();
            return q ? this.mediaItems.filter(m => (m.name || '').toLowerCase().includes(q)) : this.mediaItems;
        },
        selectMedia(item) {
            if (this.mediaPickerBlock) {
                this.mediaPickerBlock.url = item.url || item.original_url || '';
            }
            this.closeMediaPicker();
        },

        // Collapsed preview text
        previewText(block) {
            if (block.type === 'heading')   return block.text?.slice(0, 60) || '—';
            if (block.type === 'paragraph') return block.content?.replace(/<[^>]+>/g,'').slice(0, 60) || '—';
            if (block.type === 'image')     return block.url?.split('/').pop() || '—';
            if (block.type === 'video')     return block.url?.slice(0, 50) || '—';
            if (block.type === 'html')      return block.code?.replace(/<[^>]+>/g,'').slice(0, 60) || '—';
            return '';
        },

        getJson() {
            // Strip UI-only properties before serialising
            return JSON.stringify(
                this.blocks.map(({ collapsed, mediaPickerOpen, ...rest }) => rest)
            );
        },
    };
}
</script>
@endpush
