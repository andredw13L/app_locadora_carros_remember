<?php

use App\Models\Carro;

test('Preencher o carro', function() {

    $carro = new Carro;
    $carro->modelo_id = 1;
    $carro->placa = "ABC12D";
    $carro->disponivel = true;
    $carro->km = 200000;

    expect($carro->placa)->toBe('ABC12D');

    expect($carro->disponivel)->toBe(true);

    expect($carro->km)->toBe(200000);

    expect($carro->modelo_id)->toBe(1);
});

test('Verificar os atributos fillable', function(){

    $carro = new Carro;
    $fillable = $carro->getFillable();

    expect($fillable)->toContain('modelo_id', 'placa', 'disponivel', 'km');
    expect($fillable)->toContain('placa');
    expect($fillable)->toContain('disponivel');
    expect($fillable)->toContain('km');

});

test('Verificar as regras de validação', function() {

    $carro = new Carro;
    $rules = $carro->rules();

    expect($rules)->toHaveKey('modelo_id');
    expect($rules)->toHaveKey('placa');
    expect($rules)->toHaveKey('disponivel');
    expect($rules)->toHaveKey('km');

    expect($rules['modelo_id'])->toBe('required|exists:modelos,id');
    expect($rules['placa'])->toBe('required|min:6|max:6|unique:carros,placa,');
    expect($rules['disponivel'])->toBe('required|boolean');
    expect($rules['km'])->toBe('required|integer');
});


test('Verificar os Feedbacks de validação', function() {

    $carro = new Carro;
    $feedback = $carro->feedback();

    expect($feedback)->toHaveKey('required');
    expect($feedback)->toHaveKey('modelo_id.exists');
    expect($feedback)->toHaveKey('disponivel.boolean');
    expect($feedback)->toHaveKey('placa.min');
    expect($feedback)->toHaveKey('placa.max');
    expect($feedback)->toHaveKey('km.integer');


    expect($feedback['required'])->toBe('O campo :attribute é obrigatório');
    expect($feedback['modelo_id.exists'])->toBe('O modelo informado não existe');
    expect($feedback['disponivel.boolean'])->toBe('O campo disponível deve ser verdadeiro ou falso');
    expect($feedback['placa.min'])->toBe('O campo placa deve ter 6 caracteres');
    expect($feedback['placa.max'])->toBe('O campo placa deve ter 6 caracteres');
    expect($feedback['placa.unique'])->toBe('Já existe um carro com essa placa: :input');
    expect($feedback['km.integer'])->toBe('O campo km deve ser do tipo inteiro');

});
