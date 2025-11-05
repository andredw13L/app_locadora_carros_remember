<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property string $imagem Logo da marca
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Modelo> $modelos
 * @property-read int|null $modelos_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca whereImagem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Marca whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
