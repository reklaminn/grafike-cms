<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="text-center mb-8">
            <a href="/" class="text-2xl font-bold text-gray-900">{{ config('cms.name', 'IRASPA CMS') }}</a>
            <h1 class="text-xl text-gray-600 mt-2">Giriş Yap</h1>
        </div>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-3 mb-4 text-sm">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('member.login.submit') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-posta</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" class="rounded text-indigo-600">
                    Beni hatırla
                </label>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-sm">
                Giriş Yap
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Hesabınız yok mu? <a href="{{ route('member.register') }}" class="text-indigo-600 hover:underline">Kayıt Ol</a>
        </p>
    </div>
</body>
</html>
