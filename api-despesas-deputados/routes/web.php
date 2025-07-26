<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\SincronizarDespesasDeputado;
use App\Models\Deputado;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sincronizar-despesas/{id}', function ($id) {
    $deputado = Deputado::findOrFail($id);
    SincronizarDespesasDeputado::dispatch($deputado->id_camara, $deputado->id);
    return "Job enviado para o deputado: {$deputado->nome}";
});