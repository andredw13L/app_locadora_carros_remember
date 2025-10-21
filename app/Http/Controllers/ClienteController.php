<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{

    protected $cliente;

    public function __construct(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $clienteRepository = new ClienteRepository($this->cliente);

        if ($request->has('filtro')) {
            $clienteRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            /* TODO: Melhorar a segurança e implementar um DTO
                    para proteção contra SQL Injections */
            $atributos = explode(',', $request->atributos);

            $clienteRepository->selectAtributos($atributos);
        }

        return response()->json($clienteRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate($this->cliente->rules(), $this->cliente->feedback());

        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $cliente = $this->cliente->find($id);

        if ($cliente == null) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $cliente = $this->cliente->find($id);

        if ($cliente == null) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }


        if ($request->method() === 'PATCH') {

            $regrasDinamicas = [];


            foreach ($cliente->rules() as $input => $regra) {
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $cliente->feedback());
        } else {
            $request->validate($cliente->rules(), $cliente->feedback());
        }

        $cliente->fill($request->all());
        $cliente->save();

        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $cliente = $this->cliente->find($id);

        if ($cliente == null) {
            return response()->json(['message' => 'Cliente não encontrado'], 404);
        }

        $cliente->delete();

        return response()->json([
            'message' => 'O cliente foi removido com sucesso!'
        ], 200);
    }
}
