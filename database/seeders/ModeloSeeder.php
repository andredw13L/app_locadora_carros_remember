<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modelos')->insert([
            'marca_id' => DB::table('marcas')->where('nome', 'Hyundai')->value('id'),
            'nome' => 'Hyundai HB20 1.0',
            'imagem' => 'imagens/modelos/hyundai_hb20_1_0.png',
            'numero_portas' => 4,
            'lugares' => 5,
            'air_bag' => 1,
            'abs' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
