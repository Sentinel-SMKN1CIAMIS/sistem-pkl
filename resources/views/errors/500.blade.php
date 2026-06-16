@extends('errors.layout')

@section('title', __('Kesalahan Internal Server'))
@section('code', '500')
@section('icon')
    <i data-lucide="server-crash" class="w-12 h-12"></i>
@endsection
@section('message', __('Maaf, terjadi kesalahan internal pada sistem kami. Tim teknis kami sedang berupaya untuk menyelesaikan masalah ini. Silakan coba kembali nanti.'))
