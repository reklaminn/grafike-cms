<!-- Options -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Seçenekler</h3>

    <div class="space-y-3">
        <label class="flex items-center gap-2">
            <input type="hidden" name="show_in_menu" value="0">
            <input type="checkbox" name="show_in_menu" value="1"
                   {{ old('show_in_menu', $page->show_in_menu ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Menüde göster</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="show_breadcrumb" value="0">
            <input type="checkbox" name="show_breadcrumb" value="1"
                   {{ old('show_breadcrumb', $page->show_breadcrumb ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Breadcrumb göster</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="show_social_share" value="0">
            <input type="checkbox" name="show_social_share" value="1"
                   {{ old('show_social_share', $page->show_social_share ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Sosyal paylaşım butonları</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="show_facebook_comments" value="0">
            <input type="checkbox" name="show_facebook_comments" value="1"
                   {{ old('show_facebook_comments', $page->show_facebook_comments ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Facebook yorumları</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_password_protected" value="0">
            <input type="checkbox" name="is_password_protected" value="1"
                   {{ old('is_password_protected', $page->is_password_protected ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Şifre korumalı</span>
        </label>

        <div>
            <label for="link_target" class="block text-sm font-medium text-gray-700 mb-1">Bağlantı Hedefi</label>
            <select id="link_target" name="link_target"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <option value="_self" {{ old('link_target', $page->link_target ?? '_self') === '_self' ? 'selected' : '' }}>Aynı pencere</option>
                <option value="_blank" {{ old('link_target', $page->link_target ?? '') === '_blank' ? 'selected' : '' }}>Yeni pencere</option>
            </select>
        </div>
    </div>
</div>
