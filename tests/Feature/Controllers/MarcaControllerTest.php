<?php

/* TODO: Reescrever os testes de update para lidar com put e patch corretamente
          e mapear o relacionamento com modelos
*/

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Index - Deve retornar uma lista de marcas', function () {

    Storage::fake('public');

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

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste',
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/marca', $data);


    expect($response->status())->toBe(201);

    expect($response->json())->toMatchArray([
        'nome' => $data['nome'],
        'imagem' => $response->json('imagem')
    ])->toHaveKeys([
        'id',
        'created_at',
        'updated_at'
    ]);
});

test('Store - Deve retornar feedback ao tentar criar marca sem nome', function () {

    Storage::fake('public');

    $data = [
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
});


test('Store - Deve retornar feedback ao tentar criar marca com nome muito curto', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'A',
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar marca com nome muito longo', function () {

    Storage::fake('public');

    $data = [
        'nome' => str_repeat('A', 101),
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 100 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar marca sem imagem', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste'
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
});

test('Store - Deve retornar erro ao tentar criar marca com nome duplicado', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste',
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/marca', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('Já existe uma marca com esse nome: ' . $data['nome']);
});

test('Show - Deve retornar uma marca existente', function () {

    Storage::fake('public');

    $response = $this->getJson('/api/marca/1');

    expect($response->status())->toBe(200);

    expect($response->json())->toMatchArray([
        'nome' => 'Marca Teste',
        'imagem' => $response->json('imagem')
    ])->toHaveKeys([
        'id',
        'created_at',
        'updated_at'
    ]);
});

test('Show - Deve retornar erro ao tentar acessar marca inexistente', function () {

    Storage::fake('public');

    $response = $this->getJson('/api/marca/0');

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});

test('Update - Deve atualizar uma marca existente', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste - Atualizada',
        'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(200);


    expect($response->json())->toMatchArray([
        'nome' => 'Marca Teste - Atualizada',
        'imagem' => $response->json('imagem')
    ])->toHaveKeys([
        'id',
        'created_at',
        'updated_at'
    ]);
});

test('Update - Deve atualizar parcialmente uma marca existente', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste - Patch'
    ];

    $response = $this->patchJson('/api/marca/1', $data);


    expect($response->status())->toBe(200);

    expect($response->json('nome'))->toBe($data['nome']);
    expect($response->json())->toHaveKeys([
        'id',
        'imagem',
        'created_at',
        'updated_at'
    ]);
});

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome duplicado', function () {

//     Storage::fake('public');

//     $marca = $this->getJson('/api/marca/1');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => $marca->json('imagem')
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('Já existe uma marca com esse nome: ' . $data['nome']);
// });

test('Update - Deve retornar feedback ao tentar atualizar marca sem nome', function () {

    Storage::fake('public');

    $data = [
        'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
});

test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito curto', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'A',
        'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
});

test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito longo', function () {

    Storage::fake('public');

    $data = [
        'nome' => str_repeat('A', 101),
        'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 100 caracteres');
});

test('Update - Deve retornar feedback ao tentar atualizar marca sem imagem', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste - Atualizada'
    ];

    $response = $this->putJson('/api/marca/1', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
});

test('Update - Deve retornar erro ao tentar atualizar marca inexistente', function () {

    Storage::fake('public');

    $data = [
        'nome' => 'Marca Teste - Atualizada',
        'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
    ];

    $response = $this->putJson('/api/marca/0', $data);

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});

test('Destroy - Deve deletar uma marca existente', function () {

    Storage::fake('public');

    $response = $this->deleteJson('/api/marca/1');

    expect($response->status())->toBe(200);

    expect($response->json('message'))->toBe('A marca foi removida com sucesso!');

    $responseGet = $this->getJson('/api/marca/1');
    expect($responseGet->status())->toBe(404);
});

test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

    Storage::fake('public');

    $response = $this->deleteJson('/api/marca/0');

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Marca não encontrada');
});
