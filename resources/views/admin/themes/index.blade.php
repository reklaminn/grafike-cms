@extends('admin.layouts.app')

@section('title', 'Temalar')
@section('page-title', 'Temalar')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm text-gray-500">
                    Yeni builder ve block şablonlarında kullanılacak tema kayıtlarını buradan yönet. CSS/JS asset yapısı ve theme token başlangıç verileri burada tutulur.
                </p>
            </div>
            <a href="{{ route('admin.themes.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                <i class="fas fa-plus"></i> Yeni Tema
            </a>
        </div>

        <form method="GET" class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-white p-4 md:grid-cols-[minmax(0,1fr)_auto]">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Ara</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tema adı, slug veya engine..."
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-transparent focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    <i class="fas fa-search"></i> Filtrele
                </button>
                @if(request()->filled('q'))
                    <a href="{{ route('admin.themes.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                        Temizle
                    </a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            @forelse($themes as $theme)
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-gray-900">{{ $theme->name }}</h3>
                                <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">{{ $theme->slug }}</span>
                                <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">{{ $theme->engine }}</span>
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $theme->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $theme->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>
                            @if($theme->description)
                                <p class="mt-2 text-sm text-gray-500">{{ $theme->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.themes.edit', $theme) }}"
                               class="inline-flex items-center gap-2 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100">
                                <i class="fas fa-pen"></i> Düzenle
                            </a>
                            <form method="POST" action="{{ route('admin.themes.destroy', $theme) }}"
                                  onsubmit="return confirm('Bu temayı silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg px-3 py-2 text-red-500 hover:bg-red-50">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm text-gray-600 md:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 px-3 py-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">CSS Asset</dt>
                            <dd class="mt-1 font-medium text-gray-800">{{ count(data_get($theme->assets_json, 'css', [])) }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-3 py-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">JS Asset</dt>
                            <dd class="mt-1 font-medium text-gray-800">{{ count(data_get($theme->assets_json, 'js', [])) }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 px-3 py-2">
                            <dt class="text-xs uppercase tracking-wide text-gray-400">Block Şablonu</dt>
                            <dd class="mt-1 font-medium text-gray-800">{{ $theme->sectionTemplates()->count() }}</dd>
                        </div>
                    </dl>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white py-16 text-center text-gray-400">
                    <i class="fas fa-palette mb-3 text-4xl"></i>
                    <p>Henüz tema kaydı bulunmuyor.</p>
                </div>
            @endforelse
        </div>

        {{ $themes->links() }}
    </div>
@endsection
