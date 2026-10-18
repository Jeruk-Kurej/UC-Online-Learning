<?php

/*
 * This file is part of the IndoRegion package.
 *
 * (c) Azis Hapidin <azishapidin.com | azishapidin@gmail.com>
 *
 */

namespace App\Models;

use AzisHapidin\IndoRegion\Traits\ProvinceTrait;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use ProvinceTrait, \App\Traits\HasTitleCaseName;

    protected $table = 'provinces';

    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }
}
