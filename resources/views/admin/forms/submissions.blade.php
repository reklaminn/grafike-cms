@extends('admin.layouts.app')

@section('title', 'Mesajlar: ' . $form->name)
@section('page-title', 'Form Mesajları: ' . $form->name)

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.forms.edit', $form) }}" class="text-sm text-indigo-600 hover:text-indigo-700">
            <i class="fas fa-arrow-left mr-1"></i> Forma geri dön
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Veri</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50 {{ $submission->status === 'new' ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->id }}</td>
                            <td class="px-6 py-4">
                                @if(is_array($submission->data))
                                    <div class="space-y-1">
                                        @foreach($submission->data as $key => $value)
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-700">{{ ucfirst($key) }}:</span>
                                                <span class="text-gray-600">{{ \Illuminate\Support\Str::limit((string) $value, 100) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">{{ $submission->data }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->ip_address }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $submission->created_at?->format('d.m.Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">Henüz mesaj bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="px-6 py-4 border-t">{{ $submissions->links() }}</div>
        @endif
    </div>
@endsection
