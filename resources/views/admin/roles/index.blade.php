@extends('admin.layouts.app')

@section('title', 'Roller')
@section('page-title', 'Roller ve Yetkiler')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <p class="text-sm text-gray-500">Yönetici rolleri ve atanmış yetkileri yönetin.</p>
            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-plus"></i> Yeni Rol
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rol Adı</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kullanıcı Sayısı</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Yetki Sayısı</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-medium bg-indigo-50 text-indigo-700">
                                    <i class="fas fa-shield-alt mr-1.5 text-xs"></i>{{ $role->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $role->users_count }} kullanıcı</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $role->permissions->count() }} yetki</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($role->name !== 'super-admin')
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                              onsubmit="return confirm('Bu rolü silmek istediğinize emin misiniz?')">
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                Henüz rol oluşturulmamış.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
