<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $cliente_id
 * @property int $carro_id
 * @property string $data_inicio_periodo
 * @property string $data_final_previsto_periodo
 * @property string $data_final_realizado_periodo
 * @property float $valor_diaria
 * @property int $km_inicial
 * @property int $km_final
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\LocacaoFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereCarroId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereClienteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereDataFinalPrevistoPeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereDataFinalRealizadoPeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereDataInicioPeriodo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereKmFinal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereKmInicial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locacao whereValorDiaria($value)
 * @mixin \Eloquent
 */
class Locacao extends Model
{
    
    use HasFactory;

    protected $table = 'locacoes';

    protected $fillable = [
        'cliente_id', 
        'carro_id', 
        'data_inicio_periodo', 
        'data_final_previsto_periodo',
        'data_final_realizado_periodo',
        'valor_diaria',
        'km_inicial',
        'km_final'
    ];


    public function rules(): array
    {
        return [
            'cliente_id' => 'exists:clientes,id',
            'carro_id' => 'exists:carros,id'
        ];
    }


    public function feedback(): array
    {
        return [
            'cliente_id.exists' => 'O cliente informado não existe',
            'carro_id.exists' => 'O carro informado não existe'
        ];
    }
}
