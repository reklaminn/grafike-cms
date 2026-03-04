@extends('admin.layouts.app')

@section('title', 'Yeni Sayfa')
@section('page-title', 'Yeni Sayfa Oluştur')

@section('content')
    <form method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.pages._form')
    </form>
@endsection
