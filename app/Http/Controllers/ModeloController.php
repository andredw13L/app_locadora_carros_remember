<?php

namespace App\Http\Controllers;

use App\Actions\Modelo\AtualizarModelo;
use App\Actions\Modelo\ListarModelos;
use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

/**
 * @group Modelos
 *
 * Gerenciamento de modelos
 */
class ModeloController extends Controller
{

    public function __construct(protected Modelo $modelo) {}

    /**
     * Listar modelos
     *
     * Retorna a lista de modelos cadastrados, permitindo filtros,
     * seleção de atributos específicos e exibição de atributos do modelo relacionado.
     */
    public function index(Request $request, ListarModelos $listarModelos): JsonResponse
    {
        
        $modelos = $listarModelos->execute($request, $this->modelo);    
        
        return response()->json($modelos, 200);
        
    }

    /**
     * Criar um novo modelo
     *
     * Registra um novo modelo no sistema, processando dados enviados
     * pelo cliente e aplicando validações antes da criação.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->modelo->rules(), $this->modelo->feedback());

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');

        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs
        ]);

        return response()->json($modelo, 201);
    }

    /**
     * Exibir um modelo
     *
     * Retorna os detalhes de um modelo específico com base no ID informado.
     */
    public function show(int $id): JsonResponse
    {
        $modelo = $this->modelo->with('marca')->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'Modelo não encontrado'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Atualizar um modelo
     *
     * Atualiza os dados de um modelo existente. Permite atualização total (PUT)
     * ou parcial (PATCH), incluindo manipulação de atributos específicos.
     */
    public function update(Request $request, int $id, AtualizarModelo $atualizarModelo): JsonResponse
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'Modelo não encontrado'], 404);
        }

        $modelo = $atualizarModelo->execute($request, $modelo);
        
        return response()->json($modelo, 200);
    }

    /**
     * Remover um modelo
     *
     * Exclui um modelo do sistema com base no ID informado.
     */
    public function destroy(int $id): JsonResponse
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'Modelo não encontrado'], 404);
        }

        Storage::disk('public')->delete($modelo->imagem);

        $modelo->delete();
        return response()->json([
            'message' => 'O Modelo foi removido com sucesso!'
        ], 200);
    }
}
