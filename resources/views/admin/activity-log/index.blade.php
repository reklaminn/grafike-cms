@extends('admin.layouts.app')

@section('title', 'Aktivite Log')
@section('page-title', 'Aktivite Log')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Açıklama ara..."
                           class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <select name="subject_type" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tüm Modeller</option>
                    @foreach($subjectTypes as $type)
                        <option value="{{ $type }}" {{ request('subject_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($activities as $activity)
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $activity->description === 'created' ? 'bg-green-100' : ($activity->description === 'deleted' ? 'bg-red-100' : 'bg-blue-100') }}">
                                <i class="fas text-xs
                                    {{ $activity->description === 'created' ? 'fa-plus text-green-600' : ($activity->description === 'deleted' ? 'fa-trash text-red-600' : 'fa-edit text-blue-600') }}"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-800">{{ $activity->description }}</span>
                                @if($activity->subject_type)
                                    <span class="text-xs text-gray-400 ml-1">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">
                                {{ $activity->causer?->name ?? 'Sistem' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $activity->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-sm text-gray-500">
                    Henüz aktivite kaydı bulunmuyor.
                </div>
            @endforelse
        </div>

        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
@endsection
