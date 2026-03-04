{{-- Role form with permission matrix --}}
<div class="space-y-6">
    {{-- Role Name --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1 max-w-md">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Rol Adı *</label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name', $role->name ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="editör, moderatör, vb.">
            </div>
            <div class="flex gap-3 ml-4">
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    {{ isset($role) ? 'Güncelle' : 'Oluştur' }}
                </button>
                <a href="{{ route('admin.roles.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
            </div>
        </div>
    </div>

    {{-- Permission Matrix --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="permissionMatrix()">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-800">
                <i class="fas fa-key mr-2 text-amber-500"></i>Yetki Matrisi
            </h3>
            <div class="flex items-center gap-2">
                <button type="button" @click="selectAll()" class="px-3 py-1 bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100">
                    Tümünü Seç
                </button>
                <button type="button" @click="deselectAll()" class="px-3 py-1 bg-red-50 text-red-700 text-xs font-medium rounded-lg hover:bg-red-100">
                    Tümünü Kaldır
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-48">Modül</th>
                        @php
                            $allActions = ['view', 'create', 'edit', 'delete', 'upload', 'execute'];
                            $actionLabels = [
                                'view' => 'Görüntüle', 'create' => 'Oluştur', 'edit' => 'Düzenle',
                                'delete' => 'Sil', 'upload' => 'Yükle', 'execute' => 'Çalıştır'
                            ];
                        @endphp
                        @foreach($allActions as $action)
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">{{ $actionLabels[$action] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($permissionGroups as $group => $config)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-gray-800">{{ $config['label'] }}</span>
                            </td>
                            @foreach($allActions as $action)
                                <td class="px-3 py-3 text-center">
                                    @if(in_array($action, $config['actions']))
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="permissions[]"
                                                   value="{{ $group }}.{{ $action }}"
                                                   {{ in_array("{$group}.{$action}", $rolePermissions ?? []) ? 'checked' : '' }}
                                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 permission-checkbox">
                                        </label>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function permissionMatrix() {
    return {
        selectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        },
        deselectAll() {
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        }
    };
}
</script>
@endpush
