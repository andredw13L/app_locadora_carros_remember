<?php

namespace App\Actions\Marca;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AtualizarMarca
{

    public function execute(Request $request, Marca $marca): Marca
    {

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = [];

            foreach ($marca->rules() as $input => $regra) {
                if ($request->has($input)) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $dadosValidados = $request->validate($regrasDinamicas, $marca->feedback());
        } else {

            $dadosValidados = $request->validate($marca->rules(), $marca->feedback());
        }


        if ($request->file('imagem')) {

            Storage::disk('public')->delete($marca->imagem);

            $imagem = $request->file('imagem');
            $dadosValidados['imagem'] = $imagem->store('imagens/marcas', 'public');
        } else {

            $dadosValidados['imagem'] = $marca->imagem;
        }

        $marca->fill($dadosValidados)->save();

        return $marca;
    }
}
