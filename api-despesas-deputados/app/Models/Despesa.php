<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
   protected $fillable = [
        'deputado_id',
        'ano',
        'mes',
        'tipo_despesa',
        'fornecedor',
        'valor_documento',
        'url_documento',
    ];
}