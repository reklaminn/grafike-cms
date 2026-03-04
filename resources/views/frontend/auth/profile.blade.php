<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilim</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 py-12">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Profilim</h1>
            <div class="flex gap-3">
                <a href="/" class="text-sm text-gray-500 hover:text-gray-700">Ana Sayfa</a>
                <form method="POST" action="{{ route('member.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700">Çıkış Yap</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('member.profile.update') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
                <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                       class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                <input type="email" value="{{ $member->email }}" disabled
                       class="w-full px-4 py-2 border rounded-lg text-sm bg-gray-50 text-gray-500">
                <p class="text-xs text-gray-400 mt-1">E-posta değiştirilemez.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                <input type="tel" name="phone" value="{{ old('phone', $member->phone) }}"
                       class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="border-t pt-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Şifre Değiştir (opsiyonel)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Yeni Şifre</label>
                        <input type="password" name="password"
                               class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Şifre Tekrar</label>
                        <input type="password" name="password_confirmation"
                               class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Profili Güncelle
            </button>
        </form>

        {{-- Account Info --}}
        <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Hesap Bilgileri</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between"><span>Kayıt Tarihi:</span><span>{{ $member->created_at->format('d.m.Y') }}</span></div>
                @if($member->group)
                    <div class="flex justify-between"><span>Üyelik Grubu:</span><span>{{ $member->group->name }}</span></div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
