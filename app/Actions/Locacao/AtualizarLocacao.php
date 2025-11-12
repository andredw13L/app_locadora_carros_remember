<?php

namespace App\Actions\Locacao;

use App\Models\Locacao;
use Illuminate\Http\Request;

class AtualizarLocacao
{


    public function execute(Request $request, Locacao $locacao) {

         if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($locacao->rules() as $input => $regra) {
                if ($request->has($input)) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $dadosValidados = $request->validate($regrasDinamicas, $locacao->feedback());
        } else {
            $dadosValidados = $request->validate($locacao->rules(), $locacao->feedback());
        }

        $locacao->fill($dadosValidados)->save();

        return $locacao;
        
    }
}
