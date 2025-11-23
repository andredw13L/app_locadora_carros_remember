<?php

namespace App\Models;

use App\Models\Modelo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $modelo_id
 * @property string $placa
 * @property int $disponivel
 * @property int $km
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Modelo $modelo
 * @method static \Database\Factories\CarroFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereDisponivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereKm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereModeloId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro wherePlaca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carro whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Carro extends Model
{
    use HasFactory;

    protected $fillable = ['modelo_id', 'placa', 'disponivel', 'km'];


    public function rules():array
    {
        return [
            'modelo_id' => 'exists:modelos,id',
            'placa' => 'required|min:6|max:6', // TODO: substituir esa validação pra uma única
            'disponivel' => 'required|boolean',
            'km' => 'required|integer'
        ];
    }


    public function feedback(): array 
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'modelo_id.exists' => 'O modelo informado não existe',
            'placa.boolean' => 'O campo placa deve ser verdadeiro ou falso',
            'disponivel.boolean' => 'O campo disponível deve ser verdadeiro ou falso',
            'placa.min' => 'O campo placa deve ter 6 caracteres',
            'placa.max' => 'O campo placa deve ter 6 caracteres',
            'km.integer' => 'O campo km deve ser do tipo inteiro',
        ];
    }


    public function modelo(): BelongsTo {
        return $this->belongsTo(Modelo::class);
    }
}
