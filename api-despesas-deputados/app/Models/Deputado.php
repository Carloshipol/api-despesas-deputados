<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deputado extends Model
{
    protected $fillable = [
        'id_camara',
        'nome',
        'sigla_partido',
        'sigla_uf',
    ];

    public function despesas()
    {
        return $this->hasMany(Despesa::class, 'deputado_id', 'id');
    }
}