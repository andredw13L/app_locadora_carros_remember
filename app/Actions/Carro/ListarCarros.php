<?php

namespace App\Actions\Carro;

use App\Models\Carro;
use App\Repositories\CarroRepository;
use Illuminate\Http\Request;

class ListarCarros
{
    private CarroRepository $carroRepository;

    public function __construct(Carro $carro)
    {
        $this->carroRepository = new CarroRepository($carro);
    }


    public function execute(Request $request)
    {

        $carroRepository = $this->carroRepository;

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

        return $carroRepository->getResultado();
    }
}
