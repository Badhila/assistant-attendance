<?php

use App\Livewire\PresensiLabAplikasiKomputasi;
use App\Livewire\PresensiLabAplikasiProfesional;
use App\Livewire\PresensiLabJaringanKomputer;
use App\Livewire\PresensiLabPemrograman;
use App\Livewire\PresensiLabSistemInformasi;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pemrograman', PresensiLabPemrograman::class)->name('pemrograman');
Route::get('/aplikasi-komputasi', PresensiLabAplikasiKomputasi::class)->name('aplikasi-komputasi');
Route::get('/aplikasi-profesional', PresensiLabAplikasiProfesional::class)->name('aplikasi-profesional');
Route::get('/jaringan-komputer', PresensiLabJaringanKomputer::class)->name('jaringan-komputer');
Route::get('/sistem-informasi', PresensiLabSistemInformasi::class)->name('sistem-informasi');
