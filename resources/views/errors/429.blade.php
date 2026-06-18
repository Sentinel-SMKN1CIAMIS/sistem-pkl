@extends('errors.layout')

@section('title', __('Terlalu Banyak Permintaan'))
@section('code', '429')
@section('icon')
    <i data-lucide="gauge" class="w-12 h-12"></i>
@endsection
@section('message', __('Anda mengirimkan terlalu banyak permintaan dalam waktu singkat. Mohon tunggu beberapa saat sebelum mencoba mengakses kembali.'))
