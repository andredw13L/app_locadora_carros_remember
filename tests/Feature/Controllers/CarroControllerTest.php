<?php


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Verifica se o usuário está autenticado para acessar o endpoint', function () {

    $user = $this->user;

    $response = $this->authenticate($user)->getJson('/api/modelos');

    expect($response->status())->toBe(200);
});


test('Index - Deve retornar uma lista de carros', function () {

    $user = $this->user;

    $response = $this->authenticate($user)->getJson('/api/carros');

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

    $user = $this->user;

    Storage::fake('public');

    $data_marca = [
        'nome' => 'Marca Teste - Carro',
        'imagem' => UploadedFile::fake()->image('imagem_marca.png')
    ];

    $response_marca = $this->authenticate($user)->postJson('/api/marcas', $data_marca);


    expect($response_marca->status())->toBe(201);


    $marca = $this->authenticate($user)->getJson('/api/marcas/');

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

    $response_modelo = $this->authenticate($user)->postJson('/api/modelos', $data_modelo);
    
    expect($response_modelo->status())->toBe(201);

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

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

    $user = $this->user;

    Storage::fake('public');


    $data = [
        'placa' => "1D2CBA3",
        'disponivel' => false,
        'km' => 100000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O campo modelo id é obrigatório');
});


test('Store - Deve retornar erro ao tentar criar carro com placa duplicada', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('Já existe um carro com essa placa: ' . $data['placa']);
});


test('Store - Deve retornar feedback ao tentar criar carro com placa muito curta', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "A",
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar carro com placa muito longa', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => str_repeat("A", 8),
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar carro sem placa', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa é obrigatório');
});

test('Store - Deve retornar feedback ao tentar criar carro com placa que não seja string', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => random_int(1, 255),
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ser do tipo string');
});

test('Store - Deve retornar feedback ao tentar criar carro sem disponibilidade', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.disponivel.0'))->toBe('O campo disponivel é obrigatório');
});

test('Store - Deve retornar feedback ao tentar criar carro sem quilometragem', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => false,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.km.0'))->toBe('O campo km é obrigatório');
});

test('Deve retornar erro ao tentar criar carro com modelo inexistente', function() {

    $user = $this->user;

    Storage::fake('public');

    $data = [
        'modelo_id' => random_int(10, 255),
        'placa' => Str::random(7),
        'disponivel' => true,
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O modelo informado não existe');
});

test('Store - Deve retornar erro ao criar carro com disponibilidade que não seja do tipo boolean', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => random_int(3, 255),
        'km' => 200000,
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.disponivel.0'))->toBe('O campo disponível deve ser verdadeiro ou falso');
});

test('Store - Deve retornar erro ao criar carro com quilometragem que não seja do tipo inteiro', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => Str::random(7),
        'disponivel' => true,
        'km' => Str::random(),
    ];

    $response = $this->authenticate($user)->postJson('/api/carros', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.km.0'))->toBe('O campo km deve ser do tipo inteiro');
});

test('Show - Deve retornar um carro existente', function () {

    $user = $this->user;

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $carros = $this->authenticate($user)->getJson('/api/carros');

    expect($carros->status())->toBe(200);

    $carro_id = $carros->json()[0]['id'];

    $response = $this->authenticate($user)->getJson("/api/carros/{$carro_id}");

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

    $user = $this->user;

    $carros_rand = random_int(10, 255);

    $response = $this->authenticate($user)->getJson("/api/carros/{$carros_rand}");

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Carro não encontrado');
});

test('Update - Deve atualizar um carro existente', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC12DA",
        'disponivel' => false,
        'km' => 200500,
    ];

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(200);

});

test('Update - Deve atualizar parcialmente um carro existente', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'disponivel' => true,
    ];

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->patchJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(200);

});

test('Update - Deve retornar feedback ao tentar atualizar parcialmente um carro por método Put', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'disponivel' => true,
    ];

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O campo modelo id é obrigatório');
    expect($response->json('errors.placa.0'))->toBe('O campo placa é obrigatório');
    expect($response->json('errors.km.0'))->toBe('O campo km é obrigatório');

});

test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito curto', function () {

    $user = $this->user;
    
    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $data = [
        'placa' => "A",
    ];

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});

test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito longo', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $data = [
        'placa' => str_repeat("A", 8),
    ];

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.placa.0'))->toBe('O campo placa deve ter 7 caracteres');
});


test('Update - Deve retornar erro ao tentar atualizar carro inexistente', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = $this->authenticate($user)->getJson('/api/modelos/');

    expect($modelo->status())->toBe(200);

    $data = [
        'modelo_id' => $modelo->json()[0]['id'],
        'placa' => "ABC34DA",
        'disponivel' => false,
        'km' => 200500,
    ];

    $carro_id = random_int(10, 255);

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Carro não encontrado');

});


test('Update - Deve retornar erro ao tentar atualizar carro com modelo inexistente', function () {

    $user = $this->user;

    Storage::fake('public');

    $modelo = random_int(10, 255);

    $data = [
        'modelo_id' => $modelo,
        'placa' => "ABC12DA",
        'disponivel' => false,
        'km' => 200500,
    ];

    $carro = $this->authenticate($user)->getJson("/api/carros/");

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];

    $response = $this->authenticate($user)->putJson("/api/carros/{$carro_id}", $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.modelo_id.0'))->toBe('O modelo informado não existe');    

});


test('Destroy - Deve deletar uma carro existente', function () {

    $user = $this->user;

    Storage::fake('public');

    $carro = $this->authenticate($user)->getJson('/api/carros');

    expect($carro->status())->toBe(200);

    $carro_id = $carro->json()[0]['id'];
    

    $response = $this->authenticate($user)->deleteJson("/api/carros/{$carro_id}");

    expect($response->status())->toBe(200);

    expect($response->json('message'))->toBe('O carro foi removido com sucesso!');

    $responseGet = $this->authenticate($user)->getJson("/api/carros/{$carro_id}");

    expect($responseGet->status())->toBe(404);
});

test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

    $user = $this->user;

    Storage::fake('public');

    $carro_rand = random_int(10, 255);

    $response = $this->authenticate($user)->deleteJson("/api/carros/{$carro_rand}");

    expect($response->status())->toBe(404);

    expect($response->json('message'))->toBe('Carro não encontrado');
});
