<?php

namespace App\Models;

use App\Models\Modelo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carro extends Model
{
    use HasFactory;

    protected $fillable = ['modelo_id', 'placa', 'disponivel', 'km'];


    public function rules():array
    {
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required',
            'disponivel' => 'required',
            'km' => 'required'
        ];
    }


    public function feedback(): array 
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'modelo_id.exists' => 'O modelo informado não existe',
        ];
    }


    public function modelo(): BelongsTo {
        return $this->belongsTo(Modelo::class);
    }
}
