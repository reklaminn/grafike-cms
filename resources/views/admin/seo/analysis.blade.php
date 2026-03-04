@extends('admin.layouts.app')
@section('title', 'SEO Analizi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">SEO Analizi</h1>
    <a href="{{ route('admin.seo.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Geri</a>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Toplam</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <div class="text-2xl font-bold text-red-600">{{ $stats['noindex'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Noindex</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['missing_title'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Title Eksik</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['missing_description'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Desc. Eksik</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 text-center">
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['with_issues'] }}</div>
        <div class="text-xs text-gray-500 mt-1">Sorunlu</div>
    </div>
</div>

{{-- Issues List --}}
@if(count($issues) > 0)
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-4 py-3 bg-red-50 border-b">
            <h3 class="text-sm font-semibold text-red-800">{{ count($issues) }} sayfa/yazıda SEO sorunu bulundu</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Slug</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Sorunlar</th>
                    <th class="text-right px-4 py-3 font-medium text-gray-600">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($issues as $issue)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">/{{ $issue['entry']->slug }}</td>
                        <td class="px-4 py-3">
                            @foreach($issue['issues'] as $msg)
                                <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs mr-1 mb-1">{{ $msg }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.seo.edit', $issue['entry']) }}" class="text-indigo-600 hover:underline text-xs">Düzenle</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center">
        <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
        <p class="text-green-800 font-medium">Tebrikler! Tüm SEO kayıtları sorunsuz.</p>
    </div>
@endif
@endsection
