<?php

namespace App\Http\Controllers\Api;

use App\Actions\Marca\AtualizarMarca;
use App\Actions\Marca\ListarMarcas;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

/**
 * @group Marcas
 *
 * Gerenciamento de marcas
 */

class MarcaController extends Controller
{

    public function __construct(protected Marca $marca) {}



    /**
     * Listar marcas
     *
     * Retorna a lista de marcas cadastradas. Permite aplicar filtros,
     * selecionar atributos específicos e definir atributos do relacionamento
     * com modelos.
     *
     * @queryParam filtro string Filtro aplicado aos campos da marca. Exemplo: nome:Toyota No-example
     * @queryParam atributos string Lista de atributos da marca a serem retornados. Exemplo: id,nome No-example
     * @queryParam atributos_modelo string Atributos do relacionamento modelo. Exemplo: id,nome No-example
     */
    public function index(Request $request, ListarMarcas $listarMarcas): JsonResponse
    {

        $marcas = $listarMarcas->execute($request, $this->marca);

        return response()->json($marcas, 200);
    }

    /**
     * Criar uma nova marca
     *
     * Registra uma nova marca no sistema, processando dados enviados
     * pelo cliente e aplicando validações antes da criação.
     *
     * @bodyParam nome string required Nome da marca. Exemplo: Volkswagen No-example
     * @bodyParam imagem file Imagem da marca. Exemplo: logo-volkswagen.png No-example
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->marca->rules(), $this->marca->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/marcas', 'public');

        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Exibir uma marca
     *
     * Retorna os detalhes de uma marca específica com base no ID informado,
     * incluindo os modelos relacionados.
     *
     * @urlParam id integer required ID da marca. Exemplo: 1 No-example
     */
    public function show(int $id): JsonResponse
    {
        $marca = $this->marca->with('modelos')->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Atualizar uma marca
     *
     * Atualiza os dados de uma marca existente. Permite atualização total (PUT)
     * ou parcial (PATCH), processando apenas os campos enviados.
     *
     * @urlParam id integer required ID da marca. Exemplo: 1 No-example
     * @bodyParam nome string Nome da marca. Exemplo: Ford No-example
     * @bodyParam imagem file Nova imagem da marca. Exemplo: logo-ford.png No-example
     */
    public function update(Request $request, int $id, AtualizarMarca $atualizarMarca): JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        $marca = $atualizarMarca->execute($request, $marca);

        return response()->json($marca, 200);
    }


    /**
     * Remover uma marca
     *
     * Remove uma marca do sistema com base no ID informado,
     * excluindo também sua imagem associada.
     *
     * @urlParam id integer required ID da marca. Exemplo: 1 No-example
     */
    public function destroy(int $id): JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        Storage::disk('public')->delete($marca->imagem);

        $marca->delete();

        return response()->json([
            'message' => 'A marca foi removida com sucesso!'
        ], 200);
    }

}
