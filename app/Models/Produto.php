<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produto extends Model
{
    use HasFactory;
    
    
    protected $fillable = [
        'arquivo_3d',
        'capa',
        'titulo',
        'descricao',
        'valor'
    ];
}
