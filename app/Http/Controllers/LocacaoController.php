<?php

namespace App\Http\Controllers;


use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{

    public function __construct(protected Locacao $locacao)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        
        if ($request->has('filtro')) {
            $locacaoRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $atributos = explode(',', $request->atributos);

            $locacaoRepository->selectAtributos($atributos);
        }

        return response()->json($locacaoRepository->getResultado(), 200);
    }


    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $locacao = $this->locacao->find($id);


        if ($locacao === null) {
            return response()->json(['message' => 'Locação não encontrada'], 404);
        }

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($locacao->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $locacao->feedback());
        } else {
            $request->validate($locacao->rules(), $locacao->feedback());
        }

        $locacao->fill($request->all());
        $locacao->save();

        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
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
