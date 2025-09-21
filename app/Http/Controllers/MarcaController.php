<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }



    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $marcas = $this->marca->all();
        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        $regras = [
            'nome' => 'required|unique:marcas,nome',
            'imagem' => 'required'
        ];

        $feedback = [
            'required' => 'O campo :attribute é obrigatório',
            'nome.unique' => 'Já existe uma marca com esse nome: :input'
        ];

        $request->validate($regras, $feedback);

        $marca = $this->marca->create($request->all());
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        return response()->json($marca, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        $marca->update($request->all());
        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }

        $marca->delete();
        return response()->json([
            'message' => 'A marca removida com sucesso!'
        ], 200);
    }
}
