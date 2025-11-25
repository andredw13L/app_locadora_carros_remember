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
            'modelo_id' => 'required|exists:modelos,id',
            'placa' => 'required|string|size:7|unique:carros,placa,' . $this->id, // TODO: bloquear o update de placa
            'disponivel' => 'required|boolean',
            'km' => 'required|integer'
        ];
    }


    public function feedback(): array 
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'modelo_id.exists' => 'O modelo informado não existe',
            'disponivel.boolean' => 'O campo disponível deve ser verdadeiro ou falso',
            'placa.size' => 'O campo placa deve ter 7 caracteres',
            'placa.string' => 'O campo placa deve ser do tipo string',
            'km.integer' => 'O campo km deve ser do tipo inteiro',
            'placa.unique' => 'Já existe um carro com essa placa: :input',
        ];
    }


    public function modelo(): BelongsTo {
        return $this->belongsTo(Modelo::class); 
    }
}
