@extends('admin.layouts.app')
@section('title', 'Yönlendirmeler')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Yönlendirmeler</h1>
    <a href="{{ route('admin.redirects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
        <i class="fas fa-plus mr-1"></i> Yeni Yönlendirme
    </a>
</div>

<form method="GET" class="mb-6">
    <div class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="URL ara..."
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500">
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm">Ara</button>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Eski URL</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Yeni URL</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Kod</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Hit</th>
                <th class="text-center px-4 py-3 font-medium text-gray-600">Durum</th>
                <th class="text-right px-4 py-3 font-medium text-gray-600">İşlemler</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($redirects as $redirect)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $redirect->from_url }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-indigo-600">{{ $redirect->to_url }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded text-xs {{ $redirect->status_code == 301 ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $redirect->status_code }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ number_format($redirect->hit_count) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="w-2 h-2 inline-block rounded-full {{ $redirect->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('admin.redirects.edit', $redirect) }}" class="text-indigo-600 hover:underline text-xs">Düzenle</a>
                        <form method="POST" action="{{ route('admin.redirects.destroy', $redirect) }}" class="inline" onsubmit="return confirm('Silinsin mi?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">Sil</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Yönlendirme bulunamadı.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $redirects->links() }}</div>
@endsection
