@extends('admin.layouts.app')

@section('title', 'Block Şablonu Düzenle')
@section('page-title', 'Block Şablonu Düzenle')

@section('content')
    <form method="POST" action="{{ route('admin.section-templates.update', $sectionTemplate) }}">
        @csrf
        @method('PUT')
        @include('admin.section-templates._form')
    </form>
@endsection
