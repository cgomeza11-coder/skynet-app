<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'gestion-clientes')->name('clientes');
Route::livewire('/cuentas', 'gestion-cuentas')->name('cuentas');