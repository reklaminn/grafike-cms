@extends('admin.layouts.app')
@section('title', 'Medya Kütüphanesi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Medya Kütüphanesi</h1>
    <div class="flex gap-3">
        <span class="text-sm text-gray-500">{{ $stats['total'] }} dosya &middot; {{ number_format($stats['total_size'] / 1048576, 1) }} MB</span>
    </div>
</div>

{{-- Upload Area --}}
<div class="bg-white rounded-xl shadow-sm border p-6 mb-6"
     x-data="{ dragging: false, uploading: false }"
     @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
     @drop.prevent="dragging = false; handleDrop($event)"
     :class="dragging ? 'border-indigo-400 bg-indigo-50' : ''">
    <div class="text-center py-8">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>
        <p class="text-gray-600 mb-2">Dosyaları sürükleyip bırakın veya</p>
        <label class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 cursor-pointer">
            <i class="fas fa-upload mr-2"></i> Dosya Seçin
            <input type="file" class="hidden" multiple accept="{{ implode(',', array_map(fn($ext) => '.' . $ext, config('cms.media.allowed_extensions', []))) }}"
                   @change="uploadFiles($event.target.files)">
        </label>
        <p class="text-xs text-gray-400 mt-2">Maks. {{ config('cms.media.max_upload_size', 10240) / 1024 }} MB</p>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Dosya adı ara..."
           class="flex-1 min-w-[200px] px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
        <option value="">Tüm Türler</option>
        <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Resimler</option>
        <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Videolar</option>
        <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Belgeler</option>
    </select>
    <select name="collection" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
        <option value="">Tüm Koleksiyonlar</option>
        @foreach($collections as $col)
            <option value="{{ $col }}" {{ request('collection') === $col ? 'selected' : '' }}>{{ $col }}</option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">Filtrele</button>
</form>

{{-- Media Grid --}}
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @forelse($media as $item)
        <div class="group bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
            <a href="{{ route('admin.media.show', $item) }}" class="block">
                @if(str_starts_with($item->mime_type, 'image/'))
                    <div class="aspect-square overflow-hidden bg-gray-100">
                        <img src="{{ $item->getUrl() }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy">
                    </div>
                @else
                    <div class="aspect-square flex items-center justify-center bg-gray-50">
                        <div class="text-center">
                            <i class="fas fa-file text-3xl text-gray-400 mb-2"></i>
                            <div class="text-xs text-gray-500 uppercase">{{ pathinfo($item->file_name, PATHINFO_EXTENSION) }}</div>
                        </div>
                    </div>
                @endif
            </a>
            <div class="p-2">
                <p class="text-xs text-gray-700 truncate" title="{{ $item->file_name }}">{{ $item->file_name }}</p>
                <p class="text-xs text-gray-400">{{ number_format($item->size / 1024, 0) }} KB</p>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12 text-gray-400">
            <p>Henüz medya dosyası yok.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $media->links() }}</div>

<script>
function handleDrop(event) {
    const files = event.dataTransfer.files;
    if (files.length) uploadFiles(files);
}

async function uploadFiles(files) {
    for (const file of files) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const res = await fetch('{{ route("admin.media.upload") }}', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (e) {
            console.error('Upload failed:', e);
        }
    }
}
</script>
@endsection
