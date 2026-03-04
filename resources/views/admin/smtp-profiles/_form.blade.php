{{-- SMTP Profile form --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Sunucu Bilgileri</h3>
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Profil Adı *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name', $smtp_profile->name ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Ana SMTP, İletişim Formu, vb.">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label for="host" class="block text-sm font-medium text-gray-700 mb-1">SMTP Sunucu *</label>
                        <input type="text" id="host" name="host" required
                               value="{{ old('host', $smtp_profile->host ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="smtp.gmail.com">
                    </div>
                    <div>
                        <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
                        <input type="number" id="port" name="port" required
                               value="{{ old('port', $smtp_profile->port ?? 587) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="encryption" class="block text-sm font-medium text-gray-700 mb-1">Şifreleme</label>
                        <select id="encryption" name="encryption"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="tls" {{ old('encryption', $smtp_profile->encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption', $smtp_profile->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('encryption', $smtp_profile->encryption ?? '') === 'none' ? 'selected' : '' }}>Yok</option>
                        </select>
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı *</label>
                        <input type="text" id="username" name="username" required
                               value="{{ old('username', $smtp_profile->username ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Şifre {{ isset($smtp_profile) ? '' : '*' }}
                        </label>
                        <input type="password" id="password" name="password"
                               {{ isset($smtp_profile) ? '' : 'required' }}
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="{{ isset($smtp_profile) ? 'Değiştirmek için doldurun' : '' }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Gönderen Bilgileri</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="from_name" class="block text-sm font-medium text-gray-700 mb-1">Gönderen Adı *</label>
                    <input type="text" id="from_name" name="from_name" required
                           value="{{ old('from_name', $smtp_profile->from_name ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label for="from_email" class="block text-sm font-medium text-gray-700 mb-1">Gönderen E-posta *</label>
                    <input type="email" id="from_email" name="from_email" required
                           value="{{ old('from_email', $smtp_profile->from_email ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <label class="flex items-center gap-2 mb-4">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" name="is_default" value="1"
                       {{ old('is_default', $smtp_profile->is_default ?? false) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-medium text-gray-700">Varsayılan profil</span>
            </label>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-1"></i>
                    {{ isset($smtp_profile) ? 'Güncelle' : 'Oluştur' }}
                </button>
                <a href="{{ route('admin.smtp-profiles.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    İptal
                </a>
            </div>
        </div>
    </div>
</div>
