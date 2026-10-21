<?php

/*
 * This file is part of the IndoRegion package.
 *
 * (c) Azis Hapidin <azishapidin.com | azishapidin@gmail.com>
 *
 */

namespace Database\Seeders;

use AzisHapidin\IndoRegion\RawDataGetter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndoRegionProvinceSeeder extends Seeder
{
    /**
     * @deprecated
     *
     * @return void
     */
    public function run()
    {
        // --- Get Data ---
        $provinces = RawDataGetter::getProvinces();

        // --- Insert Data to Database ---
        DB::table('provinces')->insert($provinces);
    }
}
