<?php

test('Preencher a Marca', function () {
    $marca = new \App\Models\Marca();
    $marca->nome = 'Marca Teste';
    $marca->imagem = 'imagem_teste.jpg';

    //$this->assertEquals('Marca Teste', $marca->nome);
    expect($marca->nome)->toBe('Marca Teste');

    //$this->assertEquals('imagem_teste.jpg', $marca->imagem);
    expect($marca->imagem)->toBe('imagem_teste.jpg');
});

test('Verificar os Atributos Fillable', function () {
    $marca = new \App\Models\Marca();
    $fillable = $marca->getFillable();

    //$this->assertContains('nome', $fillable);
    expect($fillable)->toContain('nome');

    //$this->assertContains('imagem', $fillable);
    expect($fillable)->toContain('imagem');
});

// TODO:Testes para verificar as regras de validação
