<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\MarcaRepository;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    public function __construct(protected Marca $marca) {}



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        $marcaRepository = new MarcaRepository($this->marca);

        
        if ($request->has('atributos_modelos')) {

            $atributos_modelos = explode(',', $request->atributos_modelos);

            if (!in_array('id', $atributos_modelos)) {
                $atributos_modelos[] = 'id';
            }

            $atributos_modelos_str = 'modelos:' . implode(',', $atributos_modelos);

            $marcaRepository->selectAtributosRegistrosRelacionados(
                $atributos_modelos_str
            );
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
            $marcaRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $atributos = explode(',', $request->atributos);

            $marcaRepository->selectAtributos($atributos);
        }

        return response()->json($marcaRepository->getResultado(), 200);
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
                if ($request->has($input)) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $dadosValidados = $request->validate($regrasDinamicas, $marca->feedback());

        } else {

            $dadosValidados = $request->validate($marca->rules(), $marca->feedback());
        }


        if ($request->file('imagem')) {

            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $dadosValidados['imagem'] = $imagem->store('imagens/marcas', 'public');
        } else {
            
            $dadosValidados['imagem'] = $marca->imagem;
        }

        $marca->fill($dadosValidados)->save();

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
