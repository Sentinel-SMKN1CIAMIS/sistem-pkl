@extends('errors.layout')

@section('title', __('Halaman Tidak Ditemukan'))
@section('code', '404')
@section('icon')
    <i data-lucide="file-question" class="w-12 h-12"></i>
@endsection
@section('message', __('Maaf, halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dipindahkan, dihapus, atau Anda salah mengetikkan alamat URL.'))
