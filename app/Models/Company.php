<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    public function logoPath()
    {
        return env("MAIN_APP_URL")."/storage/logo/$this->id-logo.png";
    }
}
