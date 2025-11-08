<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Index - Deve retornar uma lista de modelos', function () {

    Storage::fake('public');

    $response = $this->getJson('/api/modelos');

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

    $response_marca = $this->postJson('/api/marcas', $data_marca);


    expect($response_marca->status())->toBe(201);


    $marca_id= $this->getJson('/api/marcas/');

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

    $response = $this->postJson('/api/modelos', $data);

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

    $response = $this->postJson('/api/modelos', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
    expect($response->json('errors.numero_portas.0'))->toBe('O campo numero portas é obrigatório');
    expect($response->json('errors.lugares.0'))->toBe('O campo lugares é obrigatório');
    expect($response->json('errors.air_bag.0'))->toBe('O campo air bag é obrigatório');
    expect($response->json('errors.abs.0'))->toBe('O campo abs é obrigatório');
});


test('Store - Deve retornar feedback ao tentar criar modelo com nome muito curto', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marcas/');

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

    $response = $this->postJson('/api/modelos', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar modelo com nome muito longo', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marcas/');

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

    $response = $this->postJson('/api/modelos', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 255 caracteres');
});

test('Store - Deve retornar feedback ao tentar criar modelo sem imagem', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo - sem imagem',
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 4,
        'air_bag' => 0,
        'abs' => 0
    ];

    $response = $this->postJson('/api/modelos', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
});

test('Store - Deve retornar erro ao tentar criar modelo com nome duplicado', function () {

    Storage::fake('public');

    $marca_id = $this->getJson('/api/marcas/');

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

    $response = $this->postJson('/api/modelos', $data);

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

    $response = $this->postJson('/api/modelos', $data);

    expect($response->status())->toBe(422);

    expect($response->json('errors.marca_id.0'))->toBe('A marca informada não existe');

});


test('Store - Deve retornar erro ao criar modelo sem lugar', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.lugares.0'))->toBe('O campo lugares é obrigatório');

});


test('Store - Deve retornar erro ao tentar criar modelo com lugar que não seja do tipo inteiro', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo lugar sem int',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 2.5,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.lugares.0'))->toBe('O número de lugares precisa ser do tipo inteiro');

});



test('Store - Deve retornar erro ao tentar criar modelo com menos de um lugar', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugares',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 0,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.lugares.0'))->toBe('O número de lugares deve estar entre 1 e 20');

});


test('Store - Deve retornar erro ao tentar criar modelo com mais de 20 lugares', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo com muitos lugares',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 21,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.lugares.0'))->toBe('O número de lugares deve estar entre 1 e 20');

});

test('Store - Deve retornar erro ao criar modelo sem porta', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'lugares' => 2,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.numero_portas.0'))->toBe('O campo numero portas é obrigatório');

});

test('Store - Deve retornar erro ao tentar criar modelo com porta que não seja do tipo inteiro', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo porta sem int',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2.5,
        'lugares' => 2,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.numero_portas.0'))->toBe('O número de portas precisa ser do tipo inteiro');

});

test('Store - Deve retornar erro ao tentar criar modelo com menos de uma porta', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem portas',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 0,
        'lugares' => 2,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.numero_portas.0'))->toBe('O número de portas deve estar entre 1 e 5');

});


test('Store - Deve retornar erro ao tentar criar modelo com mais de 5 portas', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo com muitas portas',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 6,
        'lugares' => 2,
        'air_bag' => 0,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.numero_portas.0'))->toBe('O número de portas deve estar entre 1 e 5');

});


test('Store - Deve retornar erro ao criar modelo sem air bag', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 2,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.air_bag.0'))->toBe('O campo air bag é obrigatório');

});

test('Store - Deve retornar erro ao criar modelo com air bag que não seja do tipo boolean', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 2,
        'air_bag' => 3,
        'abs' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.air_bag.0'))->toBe('O campo air bag deve ser verdadeiro ou falso');

});


test('Store - Deve retornar erro ao criar modelo sem abs', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 2,
        'air_bag' => 0

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.abs.0'))->toBe('O campo abs é obrigatório');

});

test('Store - Deve retornar erro ao criar modelo com abs que não seja do tipo boolean', function() {

    Storage::fake('public');


    $marca_id = $this->getJson('/api/marcas/');

    expect($marca_id->status())->toBe(200);

    $data = [
        'nome' => 'Modelo sem lugar',
        'imagem' => UploadedFile::fake()->image('modelo_teste.png'),
        'marca_id' => $marca_id->json()[0]['id'],
        'numero_portas' => 2,
        'lugares' => 2,
        'air_bag' => 0,
        'abs' => 3

    ];

    $response = $this->postJson('/api/modelos', $data);


    expect($response->status())->toBe(422);

    expect($response->json('errors.abs.0'))->toBe('O campo abs deve ser verdadeiro ou falso');

});


// test('Show - Deve retornar uma marca existente', function () {

//     Storage::fake('public');

//     $response = $this->getJson('/api/marcas/1');

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

//     $response = $this->getJson('/api/marcas/0');

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });

// test('Update - Deve atualizar uma marca existente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marcas/1', $data);

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

//     $response = $this->patchJson('/api/marcas/1', $data);


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

//     $response = $this->putJson('/api/marcas/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome é obrigatório');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito curto', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'A',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marcas/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no mínimo 2 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca com nome muito longo', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => str_repeat('A', 101),
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marcas/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.nome.0'))->toBe('O campo nome deve ter no máximo 100 caracteres');
// });

// test('Update - Deve retornar feedback ao tentar atualizar marca sem imagem', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada'
//     ];

//     $response = $this->putJson('/api/marcas/1', $data);

//     expect($response->status())->toBe(422);

//     expect($response->json('errors.imagem.0'))->toBe('O campo imagem é obrigatório');
// });

// test('Update - Deve retornar erro ao tentar atualizar marca inexistente', function () {

//     Storage::fake('public');

//     $data = [
//         'nome' => 'Marca Teste - Atualizada',
//         'imagem' => UploadedFile::fake()->image('imagem_teste_atualizada.png')
//     ];

//     $response = $this->putJson('/api/marcas/0', $data);

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });

// test('Destroy - Deve deletar uma marca existente', function () {

//     Storage::fake('public');

//     $response = $this->deleteJson('/api/marcas/1');

//     expect($response->status())->toBe(200);

//     expect($response->json('message'))->toBe('A marca foi removida com sucesso!');

//     $responseGet = $this->getJson('/api/marcas/1');
//     expect($responseGet->status())->toBe(404);
// });

// test('Destroy - Deve retornar erro ao tentar deletar marca inexistente', function () {

//     Storage::fake('public');

//     $response = $this->deleteJson('/api/marcas/0');

//     expect($response->status())->toBe(404);

//     expect($response->json('message'))->toBe('Marca não encontrada');
// });
