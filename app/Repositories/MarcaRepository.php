<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class MarcaRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        return $this->model = $model;
    }


    public function selectAtributosRegistrosRelacionados(string $atributos_modelos_str)
    {
        $this->model = $this->model->with($atributos_modelos_str);
    }


    public function filtro(string $filtros)
    {

        $filtros = explode(';', $filtros);

        foreach ($filtros as $chave => $valor) {

            $condicao = explode(':', $valor);

            $this->model = $this->model->where($condicao[0], $condicao[1], $condicao[2]);
        }
    }


    public function selectAtributos(array $atributos)
    {
        $this->model = $this->model->select($atributos);
    }


    public function getResultado()
    {
        return $this->model->get();
    }
}
