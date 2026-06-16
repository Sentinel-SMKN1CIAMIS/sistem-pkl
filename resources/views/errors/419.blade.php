@extends('errors.layout')

@section('title', __('Sesi Berakhir'))
@section('code', '419')
@section('icon')
    <i data-lucide="clock" class="w-12 h-12"></i>
@endsection
@section('message', __('Sesi halaman Anda telah berakhir karena tidak ada aktivitas dalam waktu lama. Silakan muat ulang halaman ini dan coba lagi.'))
