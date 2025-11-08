<?php

use App\Models\Modelo;

test('Preencher o Modelo', function () {
    $modelo = new Modelo;
    $modelo->nome = 'Modelo Teste';
    $modelo->imagem = 'imagem_teste.png';
    $modelo->marca_id = 1;

    expect($modelo->nome)->toBe('Modelo Teste');

    expect($modelo->imagem)->toBe('imagem_teste.png');

    expect($modelo->marca_id)->toBe(1);
});

test('Verificar os Atributos Fillable', function () {
    $modelo = new Modelo;
    $fillable = $modelo->getFillable();

    expect($fillable)->toContain('nome');

    expect($fillable)->toContain('imagem');

    expect($fillable)->toContain('marca_id');
});

test('Verificar as Regras de Validação', function () {
    $modelo = new Modelo;
    $rules = $modelo->rules();

    expect($rules)->toHaveKey('nome');

    expect($rules)->toHaveKey('imagem');

    expect($rules)->toHaveKey('marca_id');

    expect($rules['nome'])->toBe('required|unique:modelos,nome,|min:2|max:255');

    expect($rules['imagem'])->toBe('required|file|mimes:png,jpeg,jpg');

    expect($rules['marca_id'])->toBe('exists:marcas,id');
});

test('Verificar os Feedbacks de Validação', function () {
    $modelo = new Modelo;
    $feedback = $modelo->feedback();

    expect($feedback)->toHaveKey('required');
    expect($feedback)->toHaveKey('nome.unique');
    expect($feedback)->toHaveKey('nome.min');
    expect($feedback)->toHaveKey('marca_id.exists');
    expect($feedback)->toHaveKey('imagem.mimes');
    expect($feedback)->toHaveKey('numero_portas.between');
    expect($feedback)->toHaveKey('lugares.between');
    expect($feedback)->toHaveKey('air_bag.boolean');
    expect($feedback)->toHaveKey('abs.boolean');
    expect($feedback['required'])->toBe('O campo :attribute é obrigatório');
    expect($feedback['nome.unique'])->toBe('Já existe um modelo com esse nome: :input');
    expect($feedback['nome.min'])->toBe('O campo nome deve ter no mínimo 2 caracteres');
    expect($feedback['marca_id.exists'])->toBe('A marca informada não existe');
    expect($feedback['imagem.mimes'])->toBe('A imagem deve ser do tipo PNG, JPEG ou JPG');
    expect($feedback['numero_portas.between'])->toBe('O número de portas deve estar entre 1 e 5');
    expect($feedback['lugares.between'])->toBe('O número de lugares deve estar entre 1 e 20');
    expect($feedback['air_bag.boolean'])->toBe('O campo air_bag deve ser verdadeiro ou falso');
    expect($feedback['abs.boolean'])->toBe('O campo abs deve ser verdadeiro ou falso');
});

