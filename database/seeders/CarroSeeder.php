<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CarroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('carros')->insert([
            'modelo_id' => DB::table('modelos')->where('nome', 'Hyundai HB20 1.0')->value('id'),
            'placa' => 'BRA2E19',
            'disponivel' => true,
            'km' => random_int(1 ,2147483647),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
