<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakım Modu</title>
    @vite(['resources/css/app.css'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-lg w-full text-center px-6">
        <div class="mb-6">
            <svg class="w-24 h-24 text-indigo-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-3">Bakım Modu</h1>
        <p class="text-gray-500 mb-4">Sitemiz şu anda bakım nedeniyle geçici olarak kapalıdır.</p>
        <p class="text-sm text-gray-400">Kısa süre içerisinde tekrar hizmetinizde olacağız.</p>
    </div>
</body>
</html>
