@extends('admin.layouts.app')

@section('title', 'Yeni Yazı')
@section('page-title', 'Yeni Yazı Oluştur')

@section('content')
    @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4">
            <div class="flex items-center gap-2 font-semibold text-red-800 mb-2">
                <i class="fas fa-circle-exclamation"></i> Kayıt başarısız — lütfen hataları düzeltin:
            </div>
            <ul class="list-disc list-inside space-y-1 text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.articles._form')
    </form>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
(function () {
    const hiddenInput = document.getElementById('body');
    if (!hiddenInput) return;

    const container = document.getElementById('body-quill-editor');
    const quill = new Quill(container, {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ align: [] }],
                ['blockquote', 'code-block'],
                ['link', 'image'],
                ['clean'],
            ],
        },
    });

    // Set initial value
    const initial = hiddenInput.value;
    if (initial) {
        quill.clipboard.dangerouslyPasteHTML(initial);
    }

    // Sync to hidden textarea before form submit
    hiddenInput.closest('form').addEventListener('submit', () => {
        hiddenInput.value = quill.root.innerHTML;
    });
})();
</script>
@endpush
