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
