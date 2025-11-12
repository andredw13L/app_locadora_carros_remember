<?php

namespace App\Actions;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;


class ListarModelos
{

    private ModeloRepository $modeloRepository;

    public function __construct(Modelo $modelo)
    {
        $this->modeloRepository = new ModeloRepository($modelo);
    }


    public function execute(Request $request)
    {

        $modeloRepository = $this->modeloRepository;


        if ($request->has('atributos_marca')) {

            $atributos_marca = explode(',', $request->atributos_marca);

            if (!in_array('id', $atributos_marca)) {
                $atributos_marca[] = 'id';
            }

            $atributos_marca_str = 'marca:' . implode(',', $atributos_marca);

            $modeloRepository->selectAtributosRegistrosRelacionados(
                $atributos_marca_str
            );
        } else {
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }

        if ($request->has('filtro')) {
            $modeloRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $atributos = explode(',', $request->atributos);

            $modeloRepository->selectAtributos($atributos);
        }

        return $modeloRepository->getResultado();
    }
}
