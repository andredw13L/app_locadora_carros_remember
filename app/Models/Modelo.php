<?php

namespace App\Models;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $marca_id
 * @property string $nome
 * @property string $imagem Imagem do modelo
 * @property int $numero_portas
 * @property int $lugares
 * @property int $air_bag
 * @property int $abs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Marca $marca
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereAbs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereAirBag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereImagem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereLugares($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereMarcaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereNumeroPortas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modelo whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
            'nome' => 'required|unique:modelos,nome,' . $this->id . '|min:2|max:255',
            'imagem' => 'required|file|mimes:png,jpeg,jpg',
            'numero_portas' => 'required|integer|between:1,5',
            'lugares' => 'required|integer|between:1,20',
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
            'nome.max' => 'O campo nome deve ter no máximo 255 caracteres',
            'marca_id.exists' => 'A marca informada não existe',
            'imagem.mimes' => 'A imagem deve ser do tipo PNG, JPEG ou JPG',
            'numero_portas.between' => 'O número de portas deve estar entre 1 e 5',
            'numero_portas.integer' => 'O número de portas precisa ser do tipo inteiro',
            'lugares.between' => 'O número de lugares deve estar entre 1 e 20',
            'lugares.integer' => 'O número de lugares precisa ser do tipo inteiro',
            'air_bag.boolean' => 'O campo air_bag deve ser verdadeiro ou falso',
            'abs.boolean' => 'O campo abs deve ser verdadeiro ou falso'
        ];
    }


    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }
}
