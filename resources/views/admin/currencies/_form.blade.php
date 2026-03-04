{{-- Currency form --}}
<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Döviz Bilgileri</h3>
        <div class="space-y-4">
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kod *</label>
                    <input type="text" id="code" name="code" required maxlength="3"
                           value="{{ old('code', $currency->code ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm uppercase focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="USD">
                </div>
                <div>
                    <label for="symbol" class="block text-sm font-medium text-gray-700 mb-1">Sembol *</label>
                    <input type="text" id="symbol" name="symbol" required maxlength="10"
                           value="{{ old('symbol', $currency->symbol ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="$">
                </div>
                <div>
                    <label for="exchange_rate" class="block text-sm font-medium text-gray-700 mb-1">Kur (TRY)</label>
                    <input type="number" id="exchange_rate" name="exchange_rate" required step="0.0001" min="0"
                           value="{{ old('exchange_rate', $currency->exchange_rate ?? '0') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Döviz Adı *</label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name', $currency->name ?? '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Amerikan Doları">
            </div>
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $currency->is_active ?? true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Aktif</span>
            </label>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-save mr-1"></i> {{ isset($currency) ? 'Güncelle' : 'Oluştur' }}
            </button>
            <a href="{{ route('admin.currencies.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">İptal</a>
        </div>
    </div>
</div>
