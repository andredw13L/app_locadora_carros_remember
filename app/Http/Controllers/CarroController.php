<?php

namespace App\Http\Controllers;

use App\Actions\Carro\AtualizarCarro;
use App\Actions\Carro\ListarCarros;
use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\CarroRepository;

/**
 * @group Carros
 *
 * Gerenciamento de carros
 */
class CarroController extends Controller
{

    public function __construct(protected Carro $carro)
    {
    }


    /**
     * Listar carros
     *
     * Retorna a lista de carros cadastrados, permitindo filtros,
     * seleção de atributos específicos e exibição de atributos do modelo relacionado.
     * @queryParam atributos string Lista de atributos do carro que devem ser retornados. Separados por vírgula. Exemplo: id,placa,km,disponivel No-example
     * @queryParam atributos_modelo string Lista de atributos do modelo relacionado. Separados por vírgula. Exemplo: nome,imagem,id No-example
     * @queryParam filtro string Filtros no formato campo:operador:valor. Múltiplos filtros separados por ponto e vírgula. Exemplo: placa:=:ABC1D34; No-example
     */
    public function index(Request $request, ListarCarros $listarCarros): JsonResponse
    {
        $carros = $listarCarros->execute($request);

        return response()->json($carros, 200);
    }

    /**
     * Criar um novo carro
     *
     * Registra um novo carro no sistema, validando os dados enviados
     * pelo cliente antes de criar o registro.
     *
     * @bodyParam modelo_id integer required ID do modelo associado ao carro. Exemplo: 1 No-example
     * @bodyParam placa string required Placa do veículo. Exemplo: ABC1D23 No-example
     * @bodyParam disponivel boolean required Indica se o carro está disponível para locação. Exemplo: true No-example
     * @bodyParam km integer required Quilometragem atual do veículo. Exemplo: 15200 No-example
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Exibir um carro
     *
     * Retorna os detalhes de um carro específico com base no ID informado.
     * 
     * @urlParam id integer required O id do carro. Exemplo: 1 No-example 
     */
    public function show(int $id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if ($carro === null) {
            return response()->json(['message' => 'Carro não encontrado'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Atualizar um carro
     *
     * Atualiza os dados de um carro existente. Permite atualização total (PUT)
     * ou parcial (PATCH), aplicando validações dinâmicas conforme os campos enviados.
     * 
     * @urlParam id integer required O ID do carro a ser atualizado. Exemplo: 1 No-example
     *
     * @bodyParam modelo_id integer ID do modelo associado ao carro. Exemplo: 2 No-example
     * @bodyParam placa string Placa do veículo. Exemplo: DEF2G45 No-example
     * @bodyParam disponivel boolean Indica se o carro está disponível para locação. Exemplo: false No-example
     * @bodyParam km integer Quilometragem atual do veículo. Exemplo: 20300 No-example
     */
    public function update(Request $request, int $id, AtualizarCarro $atualizarCarro): JsonResponse
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['message' => 'Carro não encontrado'], 404);
        }

        $carro = $atualizarCarro->execute($request, $carro);

        return response()->json($carro, 200);

    }

    /**
     * Remover um carro
     *
     * Exclui um carro do sistema com base no ID informado.
     * 
     * @urlParam id integer required O id do carro. Exemplo: 1 No-example 
     */
    public function destroy(int $id): JsonResponse
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['message' => 'Carro não encontrado'], 404);
        }

        $carro->delete();

        return response()->json([
            'message' => 'O carro foi removido com sucesso!'
        ], 200);
    }
}
