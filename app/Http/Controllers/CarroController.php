<?php

namespace App\Http\Controllers;


use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Repositories\CarroRepository;

/**
 * @group Carros
 *
 * Gerenciamento de carros
 */
class CarroController extends Controller
{

    public function __construct(protected Carro $carro)
    {
    }


    /**
     * Listar carros
     *
     * Retorna a lista de carros cadastrados, permitindo filtros,
     * seleção de atributos específicos e exibição de atributos do modelo relacionado.
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
            
            $atributos = explode(',', $request->atributos);

            $carroRepository->selectAtributos($atributos);
        }

        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Criar um novo carro
     *
     * Registra um novo carro no sistema, processando dados enviados
     * pelo cliente e aplicando validações antes da criação.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->carro->rules(), $this->carro->feedback());

        $carro = $this->carro->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($carro, 201);
    }

    /**
     * Exibir um carro
     *
     * Retorna os detalhes de um carro específico com base no ID informado.
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
     * Atualizar um carro
     *
     * Atualiza os dados de um carro existente. Permite atualização total (PUT)
     * ou parcial (PATCH), incluindo manipulação de atributos específicos.
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
            $request->validate($carro->rules(), $carro->feedback());
        }

        $carro->fill($request->all());
        $carro->save();

        return response()->json($carro, 200);
    }

    /**
     * Remover um carro
     *
     * Exclui um carro do sistema com base no ID informado.
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
