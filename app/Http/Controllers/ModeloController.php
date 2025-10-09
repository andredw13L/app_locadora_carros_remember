<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    protected $modelo;

    public function __construct(Modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->modelo->all(), 200);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Modelo $modelo): JsonResponse
    {
        $modelo = $this->modelo->find($modelo->id);

        if ($modelo === null) {
            return response()->json(['message' => 'Modelo não encontrado'], 404);
        }

        return response()->json($modelo, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $modelo = $this->modelo->find($id);

        if ($modelo === null) {
            return response()->json(['message' => 'Modelo não encontrada'], 404);
        }


        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($modelo->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $modelo->feedback());
        } else {
            $request->validate($modelo->rules(), $modelo->feedback());
        }

        $modelo->fill($request->all());

        if ($request->file('imagem')) {
            // TODO: Remover a imagem do storage
            Storage::disk('public')->delete($modelo->imagem);

            // TODO: Verificar se a imagem é repetida
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/modelos', 'public');

            $modelo->imagem = $imagem_urn;
        }

        $modelo->save();

        return response()->json($modelo, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Modelo $modelo): JsonResponse
    {
        $modelo = $this->modelo->find($modelo->id);

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
