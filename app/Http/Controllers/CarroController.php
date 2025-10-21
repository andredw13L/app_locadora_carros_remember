<?php

namespace App\Http\Controllers;


use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\CarroRepository;
use Illuminate\Support\Facades\Storage;

class CarroController extends Controller
{
    protected $carro;


    public function __construct(Carro $carro)
    {
        $this->carro = $carro;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {

        $carroRepository = new CarroRepository($this->carro);

        if ($request->has('atributos_modelo')) {

            $atributo_modelo = explode(',', $request->atributos_modelo);

            if (!in_array('id', $atributo_modelo)) {
                $atributos_modelo[] = 'id';
            }

            $atributos_modelo_str = 'modelo:' . implode(',', $atributos_modelo);

            $carroRepository->selectAtributosRegistrosRelacionados(
                $atributos_modelo_str
            );
        } else {
            $carroRepository->selectAtributosRegistrosRelacionados('modelo');
        }

        if ($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            /* TODO: Melhorar a segurança e implementar um DTO
                    para proteção contra SQL Injections */
            $atributos = explode(',', $request->atributos);

            $carroRepository->selectAtributos($atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->carro->rules());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $carro = $this->carro->find($id);

        if ($carro === null) {
            return response()->json(['message' => 'Carro não encontrado'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($carro->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $carro->feedback());
        } else {
            $request->validate($carro->rules());
        }

        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
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
