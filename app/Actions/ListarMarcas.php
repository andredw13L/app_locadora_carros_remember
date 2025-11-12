<?php

namespace App\Actions;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;

class ListarMarcas
{

    private MarcaRepository $marcaRepository;

    public function __construct(private Marca $marca) 
    {
        $this->marcaRepository = new MarcaRepository($this->marca);
    }


    public function execute(Request $request)
    {
        $marcaRepository = $this->marcaRepository;

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

        return $marcaRepository->getResultado();
    }
}
