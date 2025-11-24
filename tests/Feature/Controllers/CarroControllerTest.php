<?php


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Index - Deve retornar uma lista de carros', function () {

    $response = $this->getJson('/api/carros');

    expect($response->status())->toBe(200);

    expect($response->json())->toBeArray(
        '*',
        [
            'id',
            'modelo_id', 
            'placa', 
            'disponivel', 
            'km',
            'created_at',
            'updated_at'
        ]
    );
});


test('Store - Deve criar uma novo carro', function () {

    Storage::fake('public');

    $data_marca = [
        'nome' => 'Marca Teste - Carro',
        'imagem' => UploadedFile::fake()->image('imagem_marca.png')
    ];

    $response_marca = $this->postJson('/api/marcas', $data_marca);


    expect($response_marca->status())->toBe(201);


    $marca = $this->getJson('/api/marcas/');

    expect($marca->status())->toBe(200);



    $data_modelo = [
        'nome' => 'Modelo Teste - Carro',
        'marca_id' => $marca->json()[0]['id'],
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response_modelo = $this->postJson('/api/modelos', $data_modelo);
    
    expect($response_modelo->status())->toBe(201);

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(201);


    expect($response->json())->toMatchArray([
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => $data['placa'],
        'disponivel' => $data['disponivel'],
        'km' => $data['km'],
    ])->toHaveKeys([
        'id',
        'created_at',
        'updated_at'
    ]);
});

test('Store - Deve retornar feedback ao tentar criar carro sem modelo', function () {

    Storage::fake('public');


    $data = [
        'placa' => "1D2CBA3",
        'disponivel' => false,
        'km' => 100000,
    ];

    $response = $this->postJson('/api/carros', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O campo modelo id é obrigatório');
});


test('Store - Deve retornar erro ao tentar criar carro com placa duplicada', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('Já existe um carro com essa placa: ' . $data['placa']);
});


test('Store - Deve retornar feedback ao tentar criar carro com placa muito curta', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "A",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar carro com placa muito longa', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => str_repeat("A", 8),
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar carro sem placa', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa é obrigatório');
});

test('Store - Deve retornar feedback ao tentar criar carro sem disponibilidade', function () {


    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.disponivel.0'))->toBe('O campo disponivel é obrigatório');
});

test('Store - Deve retornar feedback ao tentar criar carro sem quilometragem', function () {


    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => false,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.km.0'))->toBe('O campo km é obrigatório');
});

test('Deve retornar erro ao tentar criar carro com modelo inexistente', function() {

    Storage::fake('public');

    $data = [
        'modelo_id' => random_int(10, 255),
        'placa' => Str::random(7),
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O modelo informado não existe');
});

test('Store - Deve retornar erro ao criar carro com disponibilidade que não seja do tipo boolean', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => random_int(3, 255),
        'km' => 200000,
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.disponivel.0'))->toBe('O campo disponível deve ser verdadeiro ou falso');
});

test('Store - Deve retornar erro ao criar carro com quilometragem que não seja do tipo inteiro', function () {

    Storage::fake('public');

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => true,
        'km' => Str::random(),
    ];

    $response = $this->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.km.0'))->toBe('O campo km deve ser do tipo inteiro');
});

test('Show - Deve retornar um carro existente', function () {

    $modelo = $this->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $carros = $this->getJson('/api/carros');

    expect($carros->status())->toBe(200);

    $carro_id = $carros->json()[0]['id'];

    $response = $this->getJson("/api/carros/{$carro_id}");

    expect($response->status())->toBe(200);

    expect($response->json())->toMatchArray([
        'id' => $response->json('id'),
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => true,
        'km' => 200000,
    ])->toHaveKeys([
        'created_at',
        'updated_at'
    ]);
});

test('Show - Deve retornar erro ao tentar acessar carro inexistente', function () {

    $carros_rand = random_int(10, 255);

    $response = $this->getJson("/api/carros/{$carros_rand}");

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Carro não encontrado');
});

// test('Update - Deve atualizar um carro existente', function () {

//     Storage::fake('public');

//     $modelo = $this->getJson('/api/modelos/');

//     expect($modelo->status())->toBe(200);

//     $data = [
//         'modelo_id' => $modelo->json()[0]['id'],
//         'placa' => "ABC1D34",
//         'disponivel' => true,
//         'km' => 200000,
//     ];

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->putJson("/api/marcas/{$marca_id}", $data);

//     expect($response->status())->toBe(200);


//     expect($response->json())->toMatchArray([
//         'id' => $response->json('id'),
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => $response->json('imagem')
//     ])->toHaveKeys([
//         'created_at',
//         'updated_at'
//     ]);
// });

// test('Update - Deve atualizar parcialmente uma marca existente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Patch'
//     ];

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->patchJson("/api/marcas/{$marca_id}", $data);


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

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->putJson("/api/marcas/{$marca_id}", $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito curto', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'A',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->putJson("/api/marcas/{$marca_id}", $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito longo', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => str_repeat('A', 101),
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->putJson("/api/marcas/{$marca_id}", $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 100 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca sem imagem', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada'
//     ];

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];


//     $response = $this->putJson("/api/marcas/{$marca_id}", $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
// });

// test('Update - Deve retornar erro ao tentar atualizar marca inexistente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $marcas_rand = random_int(10, 255);

//     $response = $this->putJson("/api/marcas/{$marcas_rand}", $data);

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });

// test('Destroy - Deve deletar uma marca existente', function () {

//     Storage::fake('public');

//     $marcas = $this->getJson('/api/marcas');

//     expect($marcas->status())->toBe(200);

//     $marca_id = $marcas->json()[0]['id'];
    

//     $response = $this->deleteJson("/api/marcas/{$marca_id}");

//     expect($response->status())->toBe(200);

//     expect($response->json('message'))->toBe('A marca foi removida com sucesso!');

//     $responseGet = $this->getJson("/api/marcas/{$marca_id}");

//     expect($responseGet->status())->toBe(404);
// });

// test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

//     Storage::fake('public');

//     $marcas_rand = random_int(10, 255);

//     $response = $this->deleteJson("/api/marcas/{$marcas_rand}");

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });
