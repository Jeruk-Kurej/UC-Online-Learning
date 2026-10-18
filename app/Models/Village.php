<?php

/*
 * This file is part of the IndoRegion package.
 *
 * (c) Azis Hapidin <azishapidin.com | azishapidin@gmail.com>
 *
 */

namespace App\Models;

use AzisHapidin\IndoRegion\Traits\VillageTrait;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use VillageTrait;

    protected $table = 'villages';

    protected $hidden = [
        'district_id',
    ];
}
