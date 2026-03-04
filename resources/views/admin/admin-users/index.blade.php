@extends('admin.layouts.app')

@section('title', 'Yöneticiler')
@section('page-title', 'Yöneticiler')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <form method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ara..."
                           class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </form>
            <a href="{{ route('admin.admin-users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-plus"></i> Yeni Yönetici
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ad</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kullanıcı Adı</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">E-posta</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Son Giriş</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-sm">{{ strtoupper(substr($admin->name, 0, 1)) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800">{{ $admin->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $admin->username }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $admin->email }}</td>
                            <td class="px-6 py-4">
                                @foreach($admin->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                                @if($admin->roles->isEmpty())
                                    <span class="text-xs text-gray-400">Rol atanmamış</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $admin->last_login_at?->diffForHumans() ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.admin-users.edit', $admin) }}"
                                       class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($admin->id !== auth('admin')->id())
                                        <form method="POST" action="{{ route('admin.admin-users.destroy', $admin) }}"
                                              onsubmit="return confirm('Bu yöneticiyi silmek istediğinize emin misiniz?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                Henüz yönetici bulunmuyor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $admins->links() }}
            </div>
        @endif
    </div>
@endsection
