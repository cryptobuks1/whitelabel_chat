<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    public function logoPath()
    {
        return config('chat_app.main_app_url')."/storage/logo/$this->id-logo.png";
    }
}
