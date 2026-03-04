@extends('admin.layouts.app')

@section('title', 'Düzenle: ' . $article->title)
@section('page-title')
    Yazıyı Düzenle
    <span class="text-gray-400 font-normal text-sm ml-2">#{{ $article->id }}</span>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.articles._form')
    </form>
@endsection
