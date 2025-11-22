<?php

namespace App\Http\Controllers;

use App\Actions\Marca\AtualizarMarca;
use App\Actions\Marca\ListarMarcas;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * @group Marcas
 *
 * Gerenciamento de marcas
 */

class MarcaController extends Controller
{

    public function __construct(protected Marca $marca) {}



    /**
     * Listar Marcas
     *
     * Retorna a lista de marcas cadastradas, permitindo filtros,
     * seleção de atributos específicos e exibição de atributos do modelo relacionado.
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
     * Retorna os detalhes de uma marca específico com base no ID informado.
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
     * ou parcial (PATCH), incluindo manipulação de atributos específicos.
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
     * Exclui uma marca do sistema com base no ID informado.
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
