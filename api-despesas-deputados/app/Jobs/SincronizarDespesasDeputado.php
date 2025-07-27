<?php

namespace App\Jobs;

use App\Models\Despesa;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log; // Adiciona essa linha no topo (acima da classe)

class SincronizarDespesasDeputado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deputadoIdCamara;
    protected $deputadoDbId;

    public function __construct($deputadoIdCamara, $deputadoDbId)
    {
        $this->deputadoIdCamara = $deputadoIdCamara;
        $this->deputadoDbId = $deputadoDbId;
    }

    public function handle()
    {
        Log::info("▶️ Job iniciado para deputado ID: {$this->deputadoIdCamara}");

        $response = Http::get("https://dadosabertos.camara.leg.br/api/v2/deputados/{$this->deputadoIdCamara}/despesas", [
            'itens' => 100,
            'ordem' => 'DESC',
            'ordenarPor' => 'ano'
        ]);

        if ($response->successful()) {
            $dados = $response->json()['dados'];

            Log::info("📦 Total de despesas recebidas: " . count($dados));

            foreach ($dados as $item) {
                Log::info("Salvando despesa de {$item['nomeFornecedor']} no mês {$item['mes']}/{$item['ano']}");

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

            Log::info("Job finalizado com sucesso para deputado ID: {$this->deputadoIdCamara}");
        } else {
            Log::error("Erro ao buscar despesas do deputado ID: {$this->deputadoIdCamara}");
        }
    }
}