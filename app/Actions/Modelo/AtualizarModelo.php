<?php

namespace App\Actions\Modelo;

use App\Models\Modelo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtualizarModelo
{

    public function execute(Request $request, Modelo $modelo): Modelo {

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($modelo->rules() as $input => $regra) {
                if ($request->has($input)) { 
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $dadosValidados = $request->validate($regrasDinamicas, $modelo->feedback());

        } else {

            $dadosValidados = $request->validate($modelo->rules(), $modelo->feedback());
        }

        if ($request->file('imagem')) {

            Storage::disk('public')->delete($modelo->imagem);

            $imagem = $request->file('imagem');
            $dadosValidados['imagem'] = $imagem->store('imagens/modelos', 'public');
        } else {
            
            $dadosValidados['imagem'] = $modelo->imagem;
        }

        $modelo->fill($dadosValidados)->save();

        return $modelo;

    }

}
