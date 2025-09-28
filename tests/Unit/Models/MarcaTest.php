<?php

test('Preencher a Marca', function () {
    $marca = new \App\Models\Marca();
    $marca->nome = 'Marca Teste';
    $marca->imagem = 'imagem_teste.jpg';

    $this->assertEquals('Marca Teste', $marca->nome);
    $this->assertEquals('imagem_teste.jpg', $marca->imagem);
});

test('Verificar os Atributos Fillable', function () {
    $marca = new \App\Models\Marca();
    $fillable = $marca->getFillable();

    $this->assertContains('nome', $fillable);
    $this->assertContains('imagem', $fillable);
});
