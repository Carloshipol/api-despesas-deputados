<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Deputado;

class SincronizarDeputados extends Command
{
    protected $signature = 'sincronizar:deputados';
    protected $description = 'Sincroniza deputados da API da Câmara';

    public function handle()
    {
        $pagina = 1;
        $itensPorPagina = 100;

        do {
            $response = Http::get('https://dadosabertos.camara.leg.br/api/v2/deputados', [
                'pagina' => $pagina,
                'itens' => $itensPorPagina,
                'ordenarPor' => 'nome',
                'ordem' => 'ASC',
            ]);

            if (!$response->successful()) {
                $this->error("Erro na requisição da API");
                return 1;
            }

            $deputados = $response->json()['dados'] ?? [];

            if (count($deputados) === 0) {
                break; // Sai do loop quando não houver mais deputados
            }

            foreach ($deputados as $dep) {
                Deputado::updateOrCreate(
                    ['id_camara' => $dep['id']],
                    [
                        'nome' => $dep['nome'],
                        'sigla_partido' => $dep['siglaPartido'],
                        'sigla_uf' => $dep['siglaUf'],
                    ]
                );
            }

            $this->info("Página {$pagina} sincronizada.");

            $pagina++;

        } while (true);

        $this->info('Sincronização de deputados concluída!');
        return 0;
    }
}