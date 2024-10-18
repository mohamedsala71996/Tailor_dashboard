<?php

namespace Database\Seeders;

use App\Models\Size;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sizes = Size::insert([
            [
                'name'          => '48',
            ],
            [
                'name'          => '50',

            ],
            [
                'name'          => '52',
            ],
            [
                'name'          => '54',
            ],
            [
                'name'          => '56',
            ],
            [
                'name'          => '58',
            ],
            [
                'name'          => '60',
            ],
        ]);
    }
}
