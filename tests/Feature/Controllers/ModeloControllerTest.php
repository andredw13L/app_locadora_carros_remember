<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Index - Deve retornar uma lista de modelos', function () {

    Storage::fake('public');

    $response = $this->getJson('/api/modelo');

    expect($response->status())->toBe(200);

    expect($response->json())->toBeArray(
        '*',
        [
            'id',
            'marca_id',
            'nome',
            'imagem',
            'numero_portas',
            'lugares',
            'air_bag',
            'abs',
            'created_at',
            'updated_at'
        ]
    );
});


test('Store - Deve criar um novo modelo', function () {

    Storage::fake('public');


    $data_marca = [
        'nome' => 'Marca Teste',
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response_marca = $this->postJson('/api/marca', $data_marca);


    expect($response_marca->status())->toBe(201);


    $marca_id= $this->getJson('/api/marca/');

    expect($marca_id->status())->toBe(200);


    $data = [
        'nome' => 'Modelo Teste',
        'marca_id' => $marca_id->json()[0]['id'],
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(201);

    expect($response->json())->toMatchArray([
        'nome' => $data['nome'],
        'imagem' => $response->json('imagem'),
        'numero_portas' => $data['numero_portas'],
        'lugares' => $data['lugares'],
        'air_bag' => $data['air_bag'],
        'abs' => $data['abs']
    ])->toHaveKeys([
            'id',
            'created_at',
            'updated_at'
    ]);
});

test('Store - Deve retornar feedback ao tentar criar modelo só com a imagem', function () {

    Storage::fake('public');

    $data = [
        'imagem' => UploadedFile::fake()->image('imagem_teste.png')
    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
    expect($response->json('errors.numero_portas.0'))->toBe('O campo numero portas é obrigatório');
    expect($response->json('errors.lugares.0'))->toBe('O campo lugares é obrigatório');
    expect($response->json('errors.air_bag.0'))->toBe('O campo air bag é obrigatório');
    expect($response->json('errors.abs.0'))->toBe('O campo abs é obrigatório');
});


test('Store - Deve retornar feedback ao tentar criar modelo com nome muito curto', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marca/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'M',
        'marca_id' => $marca_id->json()[0]['id'],
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar modelo com nome muito longo', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marca/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => str_repeat('M', 256),
        'marca_id' => $marca_id->json()[0]['id'],
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 255 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar modelo sem imagem', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marca/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo - sem imagem',
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
});

test('Store - Deve retornar erro ao tentar criar marca com nome duplicado', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marca/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo Teste',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('Já existe um modelo com esse nome: ' . $data['nome']);
});


test('Store - Deve existir a marca do modelo', function () {


    Storage::fake('public');

    
        $data = [
        'nome' => 'Modelo Teste - Marca inexistente',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => random_int(10, 255),
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelo', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.marca_id.0'))->toBe('A marca informada não existe');

});

/* TODO: Criar testes para:            
            'numero_portas' => 'required|integer|digits_between:1,5',
            'lugares' => 'required|integer|digits_between:1,20',
            'air_bag' => 'required|boolean',
            'abs' => 'required|boolean'
*/


// test('Show - Deve retornar uma marca existente', function () {

//     Storage::fake('public');

//     $response = $this->getJson('/api/marca/1');

//     expect($response->status())->toBe(200);

//     expect($response->json())->toMatchArray([
//         'nome' => 'Marca Teste',
//         'imagem' => $response->json('imagem')
//     ])->toHaveKeys([
//         'id',
//         'created_at',
//         'updated_at'
//     ]);
// });

// test('Show - Deve retornar erro ao tentar acessar marca inexistente', function () {

//     Storage::fake('public');

//     $response = $this->getJson('/api/marca/0');

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });

// test('Update - Deve atualizar uma marca existente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(200);


//     expect($response->json())->toMatchArray([
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => $response->json('imagem')
//     ])->toHaveKeys([
//         'id',
//         'created_at',
//         'updated_at'
//     ]);
// });

// test('Update - Deve atualizar parcialmente uma marca existente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Patch'
//     ];

//     $response = $this->patchJson('/api/marca/1', $data);


//     expect($response->status())->toBe(200);

//     expect($response->json('nome'))->toBe($data['nome']);
//     expect($response->json())->toHaveKeys([
//         'id',
//         'imagem',
//         'created_at',
//         'updated_at'
//     ]);
// });


// test('Update - Deve retornar feedback ao tentar atualizar marca sem nome', function () {

//     Storage::fake('public');

//     $data = [
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito curto', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'A',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito longo', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => str_repeat('A', 101),
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 100 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca sem imagem', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada'
//     ];

//     $response = $this->putJson('/api/marca/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
// });

// test('Update - Deve retornar erro ao tentar atualizar marca inexistente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marca/0', $data);

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });

// test('Destroy - Deve deletar uma marca existente', function () {

//     Storage::fake('public');

//     $response = $this->deleteJson('/api/marca/1');

//     expect($response->status())->toBe(200);

//     expect($response->json('message'))->toBe('A marca foi removida com sucesso!');

//     $responseGet = $this->getJson('/api/marca/1');
//     expect($responseGet->status())->toBe(404);
// });

// test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

//     Storage::fake('public');

//     $response = $this->deleteJson('/api/marca/0');

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });
