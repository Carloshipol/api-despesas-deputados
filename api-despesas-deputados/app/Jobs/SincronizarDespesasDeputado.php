<?php

namespace App\Jobs;

use App\Models\Despesa;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SincronizarDespesasDeputado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deputadoIdCamara; // id da API (id_camara)
    protected $deputadoDbId;     // id da tabela 'deputados'

    public function __construct($deputadoIdCamara, $deputadoDbId)
    {
        $this->deputadoIdCamara = $deputadoIdCamara;
        $this->deputadoDbId = $deputadoDbId;
    }

    public function handle()
    {
        $response = Http::get("https://dadosabertos.camara.leg.br/api/v2/deputados/{$this->deputadoIdCamara}/despesas", [
            'itens' => 100,
            'ordem' => 'DESC',
            'ordenarPor' => 'ano'
        ]);

        if ($response->successful()) {
            $dados = $response->json()['dados'];

            foreach ($dados as $item) {
                Despesa::updateOrCreate(
                    [
                        'deputado_id'     => $this->deputadoDbId,
                        'ano'             => $item['ano'],
                        'mes'             => $item['mes'],
                        'fornecedor'      => $item['nomeFornecedor'],
                        'valor_documento' => $item['valorDocumento'],
                    ],
                    [
                        'tipo_despesa'    => $item['tipoDespesa'],
                        'url_documento'   => $item['urlDocumento'],
                    ]
                );
            }
        }
    }
}