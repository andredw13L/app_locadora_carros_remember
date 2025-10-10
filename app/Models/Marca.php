<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'imagem']; 

    public function rules(): array
    {
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:2|max:100',
            'imagem' => 'required|file|mimes:png'
        ];
    }

    public function feedback(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'Já existe uma marca com esse nome: :input',
            'nome.min' => 'O campo nome deve ter no mínimo 2 caracteres',
            'nome.max' => 'O campo nome deve ter no máximo 100 caracteres',
            'imagem.mimes' => 'A imagem deve ser do tipo PNG'
        ];
    }

    public function modelos(): HasMany
    {
        return $this->hasMany(Modelo::class);
    }
}
