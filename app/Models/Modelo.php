<?php

namespace App\Models;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = [
        'marca_id',
        'nome',
        'imagem',
        'numero_portas',
        'lugares',
        'air_bag',
        'abs'
    ];


    public function rules(): array
    {
        return [
            'marca_id' => 'exists:marcas,id',
            'nome' => 'required|unique:modelos,nome,' . $this->id . '|min:2',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'Já existe um modelo com esse nome: :input',
            'nome.min' => 'O campo nome deve ter no mínimo 2 caracteres',
            'marca_id.exists' => 'A marca informada não existe',
            'imagem.mimes' => 'A imagem deve ser do tipo PNG, JPEG ou JPG',
            'numero_portas.digits_between' => 'O número de portas deve estar entre 1 e 5',
            'lugares.digits_between' => 'O número de lugares deve estar entre 1 e 20',
            'air_bag.boolean' => 'O campo air_bag deve ser verdadeiro ou falso',
            'abs.boolean' => 'O campo abs deve ser verdadeiro ou falso'
        ];
    }


    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }
}
