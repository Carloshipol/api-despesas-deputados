<?php 
use App\Models\Deputado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\SincronizarDespesasDeputado;

Route::get('/deputados', function(Request $request) {
    $query = Deputado::query();

    if ($request->has('nome')) {
        $query->where('nome', 'like', '%' . $request->nome . '%');
    }

    if ($request->has('sigla_partido')) {
        $query->where('sigla_partido', $request->sigla_partido);
    }

    if ($request->has('sigla_uf')) {
        $query->where('sigla_uf', $request->sigla_uf);
    }

    $deputados = $query->paginate(10);

    return response()->json($deputados);
});


Route::post('/deputados/{id}/sincronizar-despesas', function($id) {
    $deputado = Deputado::findOrFail($id);

    SincronizarDespesasDeputado::dispatch($deputado->id_camara, $deputado->id);

    return response()->json([
        'message' => "Job de sincronização enviado para deputado: {$deputado->nome}"
    ]);
});