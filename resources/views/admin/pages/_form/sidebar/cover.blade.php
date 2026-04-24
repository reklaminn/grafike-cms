<!-- Cover Image -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Kapak Görseli</h3>

    @if(isset($page) && $page->getFirstMediaUrl('cover'))
        <div class="mb-4 relative group">
            <img src="{{ $page->getFirstMediaUrl('cover') }}"
                 class="w-full rounded-lg object-cover max-h-48" alt="">
        </div>
    @endif

    <input type="file" id="cover_image" name="cover_image" accept="image/*"
           class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    <p class="mt-2 text-xs text-gray-400">Maks. 5MB (JPG, PNG, WebP)</p>
</div>
