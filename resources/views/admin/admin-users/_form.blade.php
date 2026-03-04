{{-- Shared admin user form --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Yönetici Bilgileri</h3>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $admin_user->name ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı *</label>
                        <input type="text" id="username" name="username" required
                               value="{{ old('username', $admin_user->username ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-posta *</label>
                        <input type="email" id="email" name="email" required
                               value="{{ old('email', $admin_user->email ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Şifre {{ isset($admin_user) ? '' : '*' }}
                        </label>
                        <input type="password" id="password" name="password"
                               {{ isset($admin_user) ? '' : 'required' }}
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="{{ isset($admin_user) ? 'Değiştirmek için doldurun' : '' }}">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Şifre Tekrar</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Rol</h3>
            <select id="role" name="role"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="">— Rol Seçin —</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                            {{ old('role', isset($admin_user) && $admin_user->roles->first()?->name === $role->name ? $role->name : '') === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-gray-400">Yöneticiye atanacak yetki rolü</p>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    {{ isset($admin_user) ? 'Güncelle' : 'Oluştur' }}
                </button>
                <a href="{{ route('admin.admin-users.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
            </div>
        </div>

        @if(isset($admin_user) && $admin_user->last_login_at)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Bilgi</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Son Giriş:</dt>
                        <dd class="text-gray-800">{{ $admin_user->last_login_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Son IP:</dt>
                        <dd class="text-gray-800">{{ $admin_user->last_login_ip ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kayıt:</dt>
                        <dd class="text-gray-800">{{ $admin_user->created_at->format('d.m.Y') }}</dd>
                    </div>
                </dl>
            </div>
        @endif
    </div>
</div>
