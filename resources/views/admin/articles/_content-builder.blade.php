{{--
    Article Content Builder
    ─────────────────────────────────────────────────────────────────────
    Block types: heading | paragraph | image | video | html
    Image block uses `images` array (gallery). Legacy single `url` field is
    auto-promoted to `images: [{url,alt,caption}]` when the page loads.
--}}
@php
    // If content_json is empty but body exists, open as a single html block.
    $initialBlocks = $article->content_json ?? [];
    if (empty($initialBlocks) && !empty($article->body ?? '')) {
        $initialBlocks = [[
            'id'   => 'legacy_body',
            'type' => 'html',
            'code' => $article->body,
        ]];
    }
    // Promote legacy single-image blocks to gallery format
    $initialBlocks = array_map(function (array $b) {
        if (($b['type'] ?? '') === 'image' && !isset($b['images'])) {
            $b['images'] = empty($b['url']) ? [] : [
                ['url' => $b['url'], 'alt' => $b['alt'] ?? '', 'caption' => $b['caption'] ?? ''],
            ];
            unset($b['url'], $b['alt'], $b['caption']);
        }
        return $b;
    }, $initialBlocks);
@endphp

<div x-data="articleBlockEditor({{ Js::from($initialBlocks) }})"
     x-on:keydown.escape.window="closeMediaPicker()"
     class="space-y-3">

    {{-- Hidden input synced on submit --}}
    <input type="hidden" name="content_json" x-bind:value="getJson()">

    {{-- ─── Block list ─────────────────────────────────────────────────── --}}
    <div class="space-y-3" id="article-block-list">
        <template x-for="(block, index) in blocks" :key="block.id">
            <div class="rounded-xl border bg-white shadow-sm overflow-hidden transition-shadow hover:shadow-md"
                 :class="{
                     'border-indigo-200': block.type === 'heading',
                     'border-gray-200':   block.type === 'paragraph',
                     'border-green-200':  block.type === 'image',
                     'border-red-200':    block.type === 'video',
                     'border-amber-200':  block.type === 'html',
                 }">

                {{-- ── Block header ──────────────────────────────────────── --}}
                <div class="flex items-center gap-2 px-4 py-2.5 border-b select-none"
                     :class="{
                         'border-indigo-100 bg-indigo-50': block.type === 'heading',
                         'border-gray-100   bg-gray-50':   block.type === 'paragraph',
                         'border-green-100  bg-green-50':  block.type === 'image',
                         'border-red-100    bg-red-50':    block.type === 'video',
                         'border-amber-100  bg-amber-50':  block.type === 'html',
                     }">

                    {{-- Type badge + collapsed preview --}}
                    <button type="button" @@click="block.collapsed = !block.collapsed"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold rounded-full px-2.5 py-0.5 cursor-pointer"
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
                               'fa-images':    block.type === 'image',
                               'fa-play':      block.type === 'video',
                               'fa-code':      block.type === 'html',
                           }"></i>
                        <span x-text="{heading:'Başlık',paragraph:'Metin',image:'Görsel',video:'Video',html:'HTML'}[block.type]||block.type"></span>
                        <span x-show="block.collapsed"
                              class="font-normal text-[10px] opacity-60 truncate max-w-[220px]"
                              x-text="previewText(block)"></span>
                    </button>

                    <div class="flex-1"></div>

                    {{-- Reorder --}}
                    <button type="button" @@click="moveUp(index)" :disabled="index === 0"
                            class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-25 disabled:cursor-not-allowed" title="Yukarı">
                        <i class="fas fa-chevron-up text-xs"></i>
                    </button>
                    <button type="button" @@click="moveDown(index)" :disabled="index === blocks.length - 1"
                            class="p-1 text-gray-400 hover:text-gray-700 disabled:opacity-25 disabled:cursor-not-allowed" title="Aşağı">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    {{-- Collapse --}}
                    <button type="button" @@click="block.collapsed = !block.collapsed"
                            class="p-1.5 rounded text-gray-400 hover:text-gray-700 hover:bg-white/60"
                            :title="block.collapsed ? 'Genişlet' : 'Daralt'">
                        <i class="fas text-xs" :class="block.collapsed ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
                    </button>

                    {{-- Delete --}}
                    <button type="button" @@click="removeBlock(index)"
                            class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 ml-0.5" title="Sil">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>

                {{-- ── Block content ─────────────────────────────────────── --}}
                <div x-show="!block.collapsed" class="p-4">

                    {{-- ── HEADING ──────────────────────────────────────────── --}}
                    <div x-show="block.type === 'heading'" class="flex items-center gap-3">
                        <select x-model="block.level"
                                class="w-20 rounded-lg border border-gray-300 px-2 py-2 text-sm font-bold focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="1">H1</option>
                            <option value="2">H2</option>
                            <option value="3">H3</option>
                            <option value="4">H4</option>
                            <option value="5">H5</option>
                            <option value="6">H6</option>
                        </select>
                        <input type="text" x-model="block.text"
                               placeholder="Başlık metnini girin…"
                               class="flex-1 rounded-lg border border-gray-300 px-3 py-2 font-bold focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               :style="{ fontSize: {1:'1.6rem',2:'1.35rem',3:'1.15rem',4:'1rem',5:'.9rem',6:'.8rem'}[block.level]||'1rem' }">
                    </div>

                    {{-- ── PARAGRAPH — Quill ─────────────────────────────────── --}}
                    <div x-show="block.type === 'paragraph'"
                         x-init="block.type === 'paragraph' && $nextTick(() => {
                             if ($el.querySelector('.ql-editor')) return;
                             if (typeof Quill === 'undefined') return;
                             const q = new Quill($el.querySelector('[data-quill]'), {
                                 theme: 'snow',
                                 modules: { toolbar: [
                                     [{ header: [1, 2, 3, 4, false] }],
                                     ['bold', 'italic', 'underline', 'strike'],
                                     [{ color: [] }, { background: [] }],
                                     [{ script: 'sub' }, { script: 'super' }],
                                     ['blockquote', 'code-block'],
                                     [{ list: 'ordered' }, { list: 'bullet' }],
                                     [{ indent: '-1' }, { indent: '+1' }],
                                     [{ align: [] }],
                                     ['link'],
                                     ['clean'],
                                 ]},
                             });
                             if (block.content) q.clipboard.dangerouslyPasteHTML(block.content);
                             q.on('text-change', () => { block.content = q.root.innerHTML; });
                         })">
                        <div data-quill></div>
                    </div>

                    {{-- ── IMAGE GALLERY ─────────────────────────────────────── --}}
                    <div x-show="block.type === 'image'" class="space-y-4">

                        {{-- Image list --}}
                        <div x-show="block.images && block.images.length > 0" class="space-y-2">
                            <template x-for="(img, imgIdx) in block.images" :key="imgIdx">
                                <div class="flex gap-3 items-start rounded-xl border border-gray-200 bg-gray-50 p-3">

                                    {{-- Thumbnail --}}
                                    <div class="flex-shrink-0 w-28 h-20 rounded-lg overflow-hidden border border-gray-200 bg-gray-100">
                                        <img :src="img.url"
                                             class="w-full h-full object-cover"
                                             :alt="img.alt"
                                             @@error="$el.parentElement.innerHTML='<div class=\'flex h-full items-center justify-center text-gray-300\'><i class=\'fas fa-image text-2xl\'></i></div>'"
                                             loading="lazy">
                                    </div>

                                    {{-- Fields --}}
                                    <div class="flex-1 space-y-2 min-w-0">
                                        <input type="text" x-model="img.url"
                                               placeholder="Görsel URL'si"
                                               class="w-full rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-500 font-mono focus:ring-2 focus:ring-green-400 focus:border-transparent">
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="text" x-model="img.alt"
                                                   placeholder="Alt metni (erişilebilirlik)"
                                                   class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent">
                                            <input type="text" x-model="img.caption"
                                                   placeholder="Açıklama (caption)"
                                                   class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent">
                                        </div>
                                    </div>

                                    {{-- Image-level actions --}}
                                    <div class="flex flex-col gap-1 flex-shrink-0">
                                        <button type="button"
                                                @@click="imgIdx > 0 && block.images.splice(imgIdx-1, 2, block.images[imgIdx], block.images[imgIdx-1])"
                                                :disabled="imgIdx === 0"
                                                class="p-1.5 rounded text-gray-400 hover:text-gray-700 disabled:opacity-20 disabled:cursor-not-allowed" title="Yukarı">
                                            <i class="fas fa-chevron-up text-xs"></i>
                                        </button>
                                        <button type="button"
                                                @@click="imgIdx < block.images.length-1 && block.images.splice(imgIdx, 2, block.images[imgIdx+1], block.images[imgIdx])"
                                                :disabled="imgIdx === block.images.length - 1"
                                                class="p-1.5 rounded text-gray-400 hover:text-gray-700 disabled:opacity-20 disabled:cursor-not-allowed" title="Aşağı">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </button>
                                        <button type="button"
                                                @@click="block.images.splice(imgIdx, 1)"
                                                class="p-1.5 rounded text-red-400 hover:text-red-600 hover:bg-red-50" title="Kaldır">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Empty gallery state --}}
                        <div x-show="!block.images || block.images.length === 0"
                             class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-green-200 py-10 text-green-400">
                            <i class="fas fa-images text-3xl mb-2"></i>
                            <p class="text-sm">Henüz görsel eklenmedi</p>
                        </div>

                        {{-- Add images toolbar --}}
                        <div class="flex flex-wrap items-center gap-3 pt-1 border-t border-gray-100">
                            {{-- Media picker (multi-select) --}}
                            <button type="button" @@click="openMediaPicker(block)"
                                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 shadow-sm">
                                <i class="fas fa-folder-open"></i> Medyadan Seç
                            </button>

                            {{-- Quick URL add --}}
                            <div class="flex flex-1 min-w-56 gap-2">
                                <input type="text" x-model="block._urlInput"
                                       placeholder="URL ile ekle…"
                                       @@keydown.enter.prevent="addImageByUrl(block)"
                                       class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent">
                                <button type="button" @@click="addImageByUrl(block)"
                                        :disabled="!block._urlInput"
                                        class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-plus mr-1"></i>Ekle
                                </button>
                            </div>

                            {{-- Count badge --}}
                            <span x-show="block.images && block.images.length > 0"
                                  class="ml-auto text-xs text-gray-400"
                                  x-text="(block.images?.length || 0) + ' görsel'"></span>
                        </div>
                    </div>{{-- /image --}}

                    {{-- ── VIDEO ─────────────────────────────────────────────── --}}
                    <div x-show="block.type === 'video'" class="space-y-3">
                        <div class="flex gap-2">
                            <input type="text" x-model="block.url"
                                   @@input="parseVideoUrl(block)"
                                   placeholder="YouTube veya Vimeo URL'si yapıştırın…"
                                   class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent">
                            <span x-show="block.provider"
                                  class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 border border-red-200 px-3 text-xs font-semibold text-red-700">
                                <i class="fab" :class="block.provider === 'youtube' ? 'fa-youtube' : 'fa-vimeo'"></i>
                                <span x-text="block.provider === 'youtube' ? 'YouTube' : 'Vimeo'"></span>
                            </span>
                        </div>
                        <div x-show="block.embed_url"
                             class="rounded-xl overflow-hidden border border-gray-200 bg-black"
                             style="position:relative;padding-bottom:56.25%;height:0">
                            <iframe :src="block.embed_url" frameborder="0" allowfullscreen
                                    style="position:absolute;top:0;left:0;width:100%;height:100%"></iframe>
                        </div>
                        <input type="text" x-model="block.caption"
                               placeholder="Video açıklaması (isteğe bağlı)"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-red-400 focus:border-transparent">
                    </div>

                    {{-- ── HTML CODE ─────────────────────────────────────────── --}}
                    <div x-show="block.type === 'html'">
                        <textarea x-model="block.code" rows="8"
                                  placeholder="<div>Ham HTML buraya…</div>"
                                  spellcheck="false"
                                  class="w-full rounded-lg border border-gray-300 bg-gray-950 px-4 py-3 text-xs text-green-400 font-mono focus:ring-2 focus:ring-amber-400 focus:border-transparent resize-y leading-relaxed tracking-tight"></textarea>
                    </div>

                </div>{{-- /block content --}}
            </div>{{-- /block card --}}
        </template>
    </div>

    {{-- ─── Empty state ──────────────────────────────────────────────────── --}}
    <div x-show="blocks.length === 0"
         class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-200 py-16 text-gray-400">
        <i class="fas fa-layer-group text-4xl mb-3"></i>
        <p class="text-sm font-medium mb-1">Henüz blok eklenmedi</p>
        <p class="text-xs">Aşağıdaki düğmelerden içerik bloğu ekleyin</p>
    </div>

    {{-- ─── Add block bar ────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50/70 p-4">
        <p class="mb-3 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Blok Ekle</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="bt in blockTypes" :key="bt.type">
                <button type="button" @@click="addBlock(bt.type)"
                        class="inline-flex items-center gap-2 rounded-lg border px-4 py-2.5 text-xs font-medium transition-all hover:shadow-sm active:scale-95"
                        :class="{
                            'border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100': bt.type === 'heading',
                            'border-gray-200   bg-white   text-gray-700   hover:bg-gray-100':   bt.type === 'paragraph',
                            'border-green-200  bg-green-50 text-green-700  hover:bg-green-100':  bt.type === 'image',
                            'border-red-200    bg-red-50   text-red-700    hover:bg-red-100':    bt.type === 'video',
                            'border-amber-200  bg-amber-50 text-amber-700  hover:bg-amber-100':  bt.type === 'html',
                        }">
                    <i class="fas text-sm" :class="bt.icon"></i>
                    <span x-text="bt.label"></span>
                </button>
            </template>
        </div>
    </div>

    {{-- ─── Media Picker Modal (multi-select for gallery) ───────────────── --}}
    <template x-teleport="body">
        <div x-show="mediaPickerOpen" x-cloak
             class="fixed inset-0 z-[70] flex items-start justify-center overflow-y-auto p-4 sm:p-8"
             style="background:rgba(0,0,0,0.6);"
             @@click.self="closeMediaPicker()">

            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            <i class="fas fa-images mr-2 text-green-500"></i>Medya Kütüphanesi
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-400">
                            Görsellere tıklayarak seçin, ardından "Ekle" butonuna basın.
                        </p>
                    </div>
                    <button type="button" @@click="closeMediaPicker()"
                            class="rounded-lg p-2 text-gray-400 hover:bg-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Search --}}
                <div class="px-6 pt-4">
                    <input type="text" x-model="mediaSearch"
                           placeholder="İsim ile ara…"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-green-400 focus:border-transparent">
                </div>

                {{-- Grid --}}
                <div class="p-6">
                    <div x-show="mediaLoading" class="flex justify-center py-16 text-gray-400">
                        <i class="fas fa-circle-notch fa-spin text-3xl"></i>
                    </div>

                    <div x-show="!mediaLoading"
                         class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 max-h-[420px] overflow-y-auto pr-1">
                        <template x-for="item in filteredMedia()" :key="item.id">
                            <button type="button" @@click="toggleMediaSelection(item)"
                                    class="group relative aspect-square overflow-hidden rounded-xl border-2 transition-all"
                                    :class="isMediaSelected(item)
                                        ? 'border-green-500 ring-2 ring-green-400 ring-offset-1 shadow-md'
                                        : 'border-transparent hover:border-green-300 hover:shadow-sm'">
                                <img :src="item.thumbnail_url || item.url" :alt="item.name"
                                     class="h-full w-full object-cover" loading="lazy">
                                {{-- Selected overlay --}}
                                <div x-show="isMediaSelected(item)"
                                     class="absolute inset-0 flex items-center justify-center bg-green-500/70">
                                    <i class="fas fa-check text-white text-xl drop-shadow"></i>
                                </div>
                                {{-- Hover overlay (unselected) --}}
                                <div x-show="!isMediaSelected(item)"
                                     class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-plus text-white text-lg"></i>
                                </div>
                            </button>
                        </template>

                        <div x-show="!mediaLoading && filteredMedia().length === 0"
                             class="col-span-full py-12 text-center text-sm text-gray-400">
                            <i class="fas fa-magnifying-glass text-2xl mb-2 block opacity-40"></i>
                            Görsel bulunamadı.
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4 bg-gray-50 rounded-b-2xl">
                    <span class="text-sm text-gray-500">
                        <span x-text="mediaSelected.length" class="font-semibold text-green-600"></span>
                        görsel seçildi
                    </span>
                    <div class="flex gap-3">
                        <button type="button" @@click="closeMediaPicker()"
                                class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                            İptal
                        </button>
                        <button type="button" @@click="confirmMediaSelection()"
                                :disabled="mediaSelected.length === 0"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm">
                            <i class="fas fa-check"></i>
                            <span>Ekle</span>
                            <span x-show="mediaSelected.length > 0"
                                  class="rounded-full bg-white/30 px-1.5 py-0.5 text-xs font-bold"
                                  x-text="mediaSelected.length"></span>
                        </button>
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
            _urlInput: '',
            provider: b.provider || '',
            ...b,
            // Ensure images is always an array for image blocks
            ...(b.type === 'image' ? { images: b.images || [] } : {}),
        })),

        // ── Media picker state ─────────────────────────────────────────────
        mediaPickerOpen:  false,
        mediaPickerBlock: null,   // the image block that owns this pick session
        mediaItems:       [],
        mediaSearch:      '',
        mediaLoading:     false,
        mediaSelected:    [],     // items toggled in this session

        // ── Block type catalogue ───────────────────────────────────────────
        blockTypes: [
            { type: 'heading',   label: 'Başlık',   icon: 'fa-heading'   },
            { type: 'paragraph', label: 'Metin',    icon: 'fa-paragraph' },
            { type: 'image',     label: 'Görsel',   icon: 'fa-images'    },
            { type: 'video',     label: 'Video',    icon: 'fa-play'      },
            { type: 'html',      label: 'HTML',     icon: 'fa-code'      },
        ],

        // ── Helpers ────────────────────────────────────────────────────────
        uid() {
            return 'blk_' + Date.now() + '_' + Math.random().toString(36).slice(2, 7);
        },

        // ── Block management ───────────────────────────────────────────────
        addBlock(type) {
            const id   = this.uid();
            const defs = {
                heading:   { level: 2, text: '' },
                paragraph: { content: '' },
                image:     { images: [], _urlInput: '' },
                video:     { url: '', embed_url: '', provider: '', caption: '' },
                html:      { code: '' },
            };
            this.blocks.push({ id, type, collapsed: false, ...(defs[type] || {}) });
            this.$nextTick(() => {
                const last = this.$el.querySelector('#article-block-list > div:last-child');
                if (last) last.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        },

        removeBlock(index) {
            if (confirm('Bu bloğu silmek istiyor musunuz?')) {
                this.blocks.splice(index, 1);
            }
        },

        moveUp(index) {
            if (index > 0)
                this.blocks.splice(index - 1, 2, this.blocks[index], this.blocks[index - 1]);
        },

        moveDown(index) {
            if (index < this.blocks.length - 1)
                this.blocks.splice(index, 2, this.blocks[index + 1], this.blocks[index]);
        },

        // ── Image gallery ──────────────────────────────────────────────────
        addImageByUrl(block) {
            const url = (block._urlInput || '').trim();
            if (!url) return;
            if (!block.images) block.images = [];
            block.images.push({ url, alt: '', caption: '' });
            block._urlInput = '';
        },

        // ── Video URL parser ───────────────────────────────────────────────
        parseVideoUrl(block) {
            const url = block.url || '';
            const yt  = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&?\/\s]+)/);
            if (yt)  { block.embed_url = 'https://www.youtube.com/embed/' + yt[1];   block.provider = 'youtube'; return; }
            const vi = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
            if (vi)  { block.embed_url = 'https://player.vimeo.com/video/' + vi[1]; block.provider = 'vimeo';   return; }
            block.embed_url = '';
            block.provider  = '';
        },

        // ── Media picker ───────────────────────────────────────────────────
        openMediaPicker(block) {
            this.mediaPickerBlock = block;
            this.mediaPickerOpen  = true;
            this.mediaSelected    = [];
            this.mediaSearch      = '';
            if (!this.mediaItems.length) this.loadMedia();
        },

        closeMediaPicker() {
            this.mediaPickerOpen  = false;
            this.mediaPickerBlock = null;
            this.mediaSelected    = [];
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
            const q = this.mediaSearch.trim().toLowerCase();
            return q
                ? this.mediaItems.filter(m => (m.name || '').toLowerCase().includes(q))
                : this.mediaItems;
        },

        toggleMediaSelection(item) {
            const idx = this.mediaSelected.findIndex(s => s.id === item.id);
            if (idx === -1) this.mediaSelected.push(item);
            else            this.mediaSelected.splice(idx, 1);
        },

        isMediaSelected(item) {
            return this.mediaSelected.some(s => s.id === item.id);
        },

        confirmMediaSelection() {
            if (!this.mediaPickerBlock) { this.closeMediaPicker(); return; }
            if (!this.mediaPickerBlock.images) this.mediaPickerBlock.images = [];
            for (const item of this.mediaSelected) {
                this.mediaPickerBlock.images.push({
                    url:     item.url || item.original_url || '',
                    alt:     item.name || '',
                    caption: '',
                });
            }
            this.closeMediaPicker();
        },

        // ── Preview text for collapsed block header ────────────────────────
        previewText(block) {
            if (block.type === 'heading')
                return block.text?.slice(0, 70) || '—';
            if (block.type === 'paragraph')
                return (block.content || '').replace(/<[^>]+>/g, '').trim().slice(0, 70) || '—';
            if (block.type === 'image') {
                const n = block.images?.length || 0;
                return n === 0 ? 'Görsel yok' : n + ' görsel';
            }
            if (block.type === 'video')
                return block.url?.slice(0, 60) || '—';
            if (block.type === 'html')
                return (block.code || '').replace(/<[^>]+>/g, '').trim().slice(0, 70) || '—';
            return '';
        },

        // ── Serialise to JSON (strip UI-only keys) ─────────────────────────
        getJson() {
            return JSON.stringify(
                this.blocks.map(({ collapsed, _urlInput, ...rest }) => rest)
            );
        },
    };
}
</script>
@endpush
