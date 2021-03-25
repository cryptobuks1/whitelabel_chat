<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Consumer extends Model
{
    protected $table = "consumer_logins";

    public function getCompanyIdAttribute()
    {
        $cids = DB::table('consumers')->where('consumer_login_id', $this->id)->select('company_id')->get()->toArray();
        $company_ids = array_map(function($item){
            return $item->company_id;
        }, $cids);
        return implode('|', $company_ids);
    }

    public function getPhoneNoAttribute()
    {
        return DB::table('consumers')->where('consumer_login_id', $this->id)->first()->mobile_no ?? '';
    }

    public function getNameAttribute()
    {
        return DB::table('consumers')->where('consumer_login_id', $this->id)->first()->name ?? '';
    }

}
