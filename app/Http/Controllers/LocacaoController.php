<?php

namespace App\Http\Controllers;

use App\Actions\Locacao\AtualizarLocacao;
use App\Actions\Locacao\ListarLocacoes;
use App\Models\Locacao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Locações
 *
 * Gerenciamento de locações
 */
class LocacaoController extends Controller
{

    public function __construct(protected Locacao $locacao)
    {
    }



    /**
     * Listar locações
     *
     * Retorna a lista de locações cadastradas, permitindo filtros,
     * seleção de atributos específicos e exibição de atributos do locação relacionado.
     */
    public function index(Request $request, ListarLocacoes $listarLocacoes): JsonResponse
    {

        $locacoes = $listarLocacoes->execute($request, $this->locacao);

        return response()->json($locacoes, 200);
    }


    /**
     * Criar uma nova locação
     *
     * Registra uma nova locação no sistema, processando dados enviados
     * pelo cliente e aplicando validações antes da criação.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->locacao->rules(), $this->locacao->feedback());

        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final
        ]);

        return response()->json($locacao, 201);
    }

    /**
     * Exibir uma locação
     *
     * Retorna os detalhes de uma locação específico com base no ID informado.
     */
    public function show(int $id): JsonResponse
    {
        $locacao = $this->locacao->find($id);

        if ($locacao === null) {
            return response()->json(['message' => 'Locação não encontrada'], 404);
        }

        return response()->json($locacao, 201);
    }

    /**
     * Atualizar uma locação
     *
     * Atualiza os dados de uma locação existente. Permite atualização total (PUT)
     * ou parcial (PATCH), incluindo manipulação de atributos específicos.
     */
    public function update(Request $request, int $id, AtualizarLocacao $atualizarLocacao): JsonResponse
    {
        $locacao = $this->locacao->find($id);


        if ($locacao === null) {
            return response()->json(['message' => 'Locação não encontrada'], 404);
        }

        $locacao = $atualizarLocacao($request, $locacao);

        return response()->json($locacao, 200);
    }

    /**
     * Remover uma locação
     *
     * Exclui uma locação do sistema com base no ID informado.
     */
    public function destroy(int $id): JsonResponse
    {
        $locacao = $this->locacao->find($id);


        if ($locacao === null) {
            return response()->json(['message' => 'Locação não encontrada'], 404);
        }

        $locacao->delete();

        return response()->json([
            'message' => 'A locação foi removida com sucesso!'
        ], 200);
    }
}
