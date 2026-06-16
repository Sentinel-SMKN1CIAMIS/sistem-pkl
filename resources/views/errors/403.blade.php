@extends('errors.layout')

@section('title', __('Akses Ditolak'))
@section('code', '403')
@section('icon')
    <i data-lucide="shield-alert" class="w-12 h-12"></i>
@endsection
@section('message', $exception->getMessage() ?: __('Anda tidak memiliki hak akses untuk membuka halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah sebuah kesalahan.'))
