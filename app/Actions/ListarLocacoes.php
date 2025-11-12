<?php

namespace App\Actions;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class ListarLocacoes
{

    private LocacaoRepository $locacaoRepository;

    public function __construct(private Locacao $locacao) {
        $this->locacaoRepository = new LocacaoRepository($locacao);
    }


    public function execute(Request $request)
    {
        $locacaoRepository = $this->locacaoRepository;


        if ($request->has('filtro')) {
            $locacaoRepository->filtro($request->filtro);
        }

        if ($request->has('atributos')) {
            $atributos = explode(',', $request->atributos);

            $locacaoRepository->selectAtributos($atributos);
        }

        return $locacaoRepository->getResultado();
    }
}
