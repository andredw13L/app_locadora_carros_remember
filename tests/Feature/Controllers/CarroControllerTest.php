<?php


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('Index - Deve retornar uma lista de carros', function () {

    Storage::fake('public');

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
