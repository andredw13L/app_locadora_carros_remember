<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request): JsonResponse
    {
        $marcas = [];

        if ($request->has('atributos_modelos')) {

            $atributos_modelos = explode(',', $request->atributos_modelos);

            if (!in_array('id', $atributos_modelos)) {
                $atributos_modelos[] = 'id';
            }

            $atributos_modelos_str = 'marca:' . implode(',', $atributos_modelos);

            $marcas = $this->marca->with($atributos_modelos_str);
        } else {
            $marcas = $this->marca->with('modelos');
        }

        if ($request->has('filtro')) {

            $filtros = explode(';', $request->filtro);

            foreach ($filtros as $chave => $valor) {

                $condicao = explode(':', $valor);

                $marcas = $marcas->where($condicao[0], $condicao[1], $condicao[2]);
            }
        }

        if ($request->has('atributos')) {
            /* TODO: Melhorar a segurança e implementar um DTO
                    para proteção contra SQL Injections */
            $atributos = explode(',', $request->atributos);

            $marcas = $marcas->select($atributos)->get();
        } else {

            $marcas = $marcas->get();
        }

        return response()->json($marcas, 200);
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['message' => 'Marca não encontrada'], 404);
        }


        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($marca->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        } else {
            $request->validate($marca->rules(), $marca->feedback());
        }


        if ($request->file('imagem')) {

            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/marcas', 'public');
        } else {

            $imagem_urn = $marca->imagem;
        }

        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        $marca->save();

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
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
