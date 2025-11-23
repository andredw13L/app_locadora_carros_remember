<?php

use App\Models\Marca;

test('Preencher a Marca', function () {
    $marca = new Marca;
    $marca->nome = 'Marca Teste';
    $marca->imagem = 'imagem_teste.png';

    //$this->assertEquals('Marca Teste', $marca->nome);
    expect($marca->nome)->toBe('Marca Teste');

    //$this->assertEquals('imagem_teste.jpg', $marca->imagem);
    expect($marca->imagem)->toBe('imagem_teste.png');
});

test('Verificar os Atributos Fillable', function () {
    $marca = new Marca;
    $fillable = $marca->getFillable();

    //$this->assertContains('nome', $fillable);
    expect($fillable)->toContain('nome');

    //$this->assertContains('imagem', $fillable);
    expect($fillable)->toContain('imagem');
});

test('Verificar as Regras de Validação', function () {
    $marca = new Marca;
    $rules = $marca->rules();

    expect($rules)->toHaveKey('nome');

    expect($rules)->toHaveKey('imagem');

    expect($rules['nome'])->toBe('required|unique:marcas,nome,|min:2|max:100');

    expect($rules['imagem'])->toBe('required|file|mimes:png');
});

test('Verificar os Feedbacks de Validação', function () {
    $marca = new Marca;
    $feedback = $marca->feedback();

    expect($feedback)->toHaveKey('required');

    expect($feedback)->toHaveKey('nome.unique');

    expect($feedback)->toHaveKey('nome.min');

    expect($feedback)->toHaveKey('nome.max');

    expect($feedback)->toHaveKey('imagem.mimes');

    expect($feedback['required'])->toBe('O campo :attribute é obrigatório');

    expect($feedback['nome.unique'])->toBe('Já existe uma marca com esse nome: :input');

    expect($feedback['nome.min'])->toBe('O campo nome deve ter no mínimo 2 caracteres');

    expect($feedback['nome.max'])->toBe('O campo nome deve ter no máximo 100 caracteres');

    expect($feedback['imagem.mimes'])->toBe('A imagem deve ser do tipo PNG');
});
