<?php

test('Index - Deve retornar uma lista de marcas', function () {

    $response = $this->getJson('/api/marca');

    expect($response->status())->toBe(200);

    expect($response->json())->toBeArray(
        '*',
        [
            'id',
            'nome',
            'imagem',
            'created_at',
            'updated_at'
        ]
    );
});


test('Store - Deve criar uma nova marca', function () {

    $data = [
        'nome' => 'Marca Teste',
        'imagem' => 'imagem_teste.jpg'
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(201);

    expect($response->json())->toMatchArray($data);
});


test('Store - Deve retornar erro ao tentar criar marca com nome duplicado', function () {

    $data = [
        'nome' => 'Marca Teste',
        'imagem' => 'imagem_teste.jpg'
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('Já existe uma marca com esse nome: ' . $data['nome']);
});

test('Show - Deve retornar uma marca existente', function () {

    $response = $this->getJson('/api/marca/1');

    expect($response->status())->toBe(200);

    expect($response->json())->toMatchArray([
        'id' => 1,
        'nome' => 'Marca Teste',
        'imagem' => 'imagem_teste.jpg'
    ])->toHaveKeys([
        'created_at',
        'updated_at'
    ]);

});

test('Show - Deve retornar erro ao tentar acessar marca inexistente', function () {

    $response = $this->getJson('/api/marca/0');

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});

test('Update - Deve atualizar uma marca existente', function () {

    $data = [
        'nome' => 'Marca Teste - Atualizada',
        'imagem' => 'imagem_teste_atualizada.jpg'
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(200);

    expect($response->json())->toMatchArray($data);
});

test('Update - Deve retornar erro ao tentar atualizar marca inexistente', function () {

    $data = [
        'nome' => 'Marca Teste - Atualizada',
        'imagem' => 'imagem_teste_atualizada.jpg'
    ];

    $response = $this->putJson('/api/marca/0', $data);

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});

test('Destroy - Deve deletar uma marca existente', function () {

    $response = $this->deleteJson('/api/marca/1');

    expect($response->status())->toBe(200);

    expect($response->json('message'))->toBe('A marca foi removida com sucesso!');

    $responseGet = $this->getJson('/api/marca/1');
    expect($responseGet->status())->toBe(404);
});

test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

    $response = $this->deleteJson('/api/marca/0');

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});
