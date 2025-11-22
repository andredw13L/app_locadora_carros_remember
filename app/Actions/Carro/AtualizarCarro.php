<?php

namespace App\Actions\Carro;

use App\Models\Carro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtualizarCarro
{

    public function execute(Request $request, Carro $carro): Carro {

        if ($request->method() === 'PATCH') {
            $regrasDinamicas = [];

            foreach ($carro->rules() as $input => $regra) {
                if ($request->has($input)) { 
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $dadosValidados = $request->validate($regrasDinamicas, $carro->feedback());

        } else {

            $dadosValidados = $request->validate($carro->rules(), $carro->feedback());
        }

        if ($request->file('imagem')) {

            Storage::disk('public')->delete($carro->imagem);

            $imagem = $request->file('imagem');
            $dadosValidados['imagem'] = $imagem->store('imagens/carros', 'public');
        } else {
            
            $dadosValidados['imagem'] = $carro->imagem;
        }

        $carro->update($dadosValidados);

        return $carro;

    }

}
