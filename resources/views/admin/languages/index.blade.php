@extends('admin.layouts.app')
@section('title', 'Dil Yönetimi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dil Yönetimi</h1>
    <div class="flex gap-3">
        <a href="{{ route('admin.languages.translations') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
            <i class="fas fa-language mr-1"></i> Çeviriler
        </a>
        <a href="{{ route('admin.languages.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-plus mr-1"></i> Yeni Dil
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($languages as $lang)
        <div class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg">{{ $lang->name }}</h3>
                    <span class="text-xs text-gray-500 font-mono uppercase">{{ $lang->code }}</span>
                </div>
                <span class="w-3 h-3 rounded-full {{ $lang->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
            </div>
            <div class="flex gap-4 text-xs text-gray-500 mb-4">
                <span>{{ $lang->pages_count }} sayfa</span>
                <span>{{ $lang->articles_count }} yazı</span>
                <span>{{ $lang->direction }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.languages.edit', $lang) }}" class="flex-1 text-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs hover:bg-gray-200">Düzenle</a>
                <form method="POST" action="{{ route('admin.languages.destroy', $lang) }}" class="inline" onsubmit="return confirm('Bu dili silmek istediğinize emin misiniz?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs hover:bg-red-100">Sil</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
