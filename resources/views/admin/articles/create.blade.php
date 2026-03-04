@extends('admin.layouts.app')

@section('title', 'Yeni Yazı')
@section('page-title', 'Yeni Yazı Oluştur')

@section('content')
    <form method="POST" action="{{ route('admin.articles.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.articles._form')
    </form>
@endsection
