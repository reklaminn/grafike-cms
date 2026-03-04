@extends('admin.layouts.app')

@section('title', 'Tasarım (CSS/JS)')
@section('page-title', 'Tasarım Editörü')

@section('content')
<form method="POST" action="{{ route('admin.design.update') }}" x-data="{ activeTab: 'css' }">
    @csrf
    @method('PUT')

    {{-- Tabs --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="flex items-center justify-between border-b border-gray-200 px-6">
            <div class="flex gap-1">
                <button type="button" @click="activeTab = 'css'"
                        :class="activeTab === 'css' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                    <i class="fab fa-css3-alt mr-1"></i> Global CSS
                </button>
                <button type="button" @click="activeTab = 'js'"
                        :class="activeTab === 'js' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                    <i class="fab fa-js mr-1"></i> Global JS
                </button>
                <button type="button" @click="activeTab = 'header'"
                        :class="activeTab === 'header' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                    <i class="fas fa-code mr-1"></i> Header Scripts
                </button>
                <button type="button" @click="activeTab = 'footer'"
                        :class="activeTab === 'footer' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                    <i class="fas fa-code mr-1"></i> Footer Scripts
                </button>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-1"></i> Kaydet
            </button>
        </div>

        {{-- CSS Editor --}}
        <div x-show="activeTab === 'css'" class="p-6">
            <input type="hidden" name="assets[0][id]" value="{{ $globalCss->id }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Global CSS (tüm sayfalarda)</label>
            <textarea name="assets[0][content]" rows="20"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono bg-gray-900 text-green-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      spellcheck="false">{{ $globalCss->content }}</textarea>
        </div>

        {{-- JS Editor --}}
        <div x-show="activeTab === 'js'" x-cloak class="p-6">
            <input type="hidden" name="assets[1][id]" value="{{ $globalJs->id }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Global JavaScript (tüm sayfalarda)</label>
            <textarea name="assets[1][content]" rows="20"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono bg-gray-900 text-yellow-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      spellcheck="false">{{ $globalJs->content }}</textarea>
        </div>

        {{-- Header Scripts --}}
        <div x-show="activeTab === 'header'" x-cloak class="p-6">
            <input type="hidden" name="assets[2][id]" value="{{ $headerScripts->id }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Header Scripts (&lt;head&gt; etiketine eklenir)</label>
            <p class="text-xs text-gray-400 mb-3">GTM, Analytics, Meta doğrulama, vb. kodları buraya ekleyin.</p>
            <textarea name="assets[2][content]" rows="15"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono bg-gray-900 text-blue-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      spellcheck="false">{{ $headerScripts->content }}</textarea>
        </div>

        {{-- Footer Scripts --}}
        <div x-show="activeTab === 'footer'" x-cloak class="p-6">
            <input type="hidden" name="assets[3][id]" value="{{ $footerScripts->id }}">
            <label class="block text-sm font-medium text-gray-700 mb-2">Footer Scripts (&lt;/body&gt; öncesine eklenir)</label>
            <p class="text-xs text-gray-400 mb-3">Chat widget, izleme kodu, vb. footer scriptleri.</p>
            <textarea name="assets[3][content]" rows="15"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm font-mono bg-gray-900 text-purple-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      spellcheck="false">{{ $footerScripts->content }}</textarea>
        </div>
    </div>
</form>
@endsection
