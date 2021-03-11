<?php

namespace App\Models;

use Eloquent as Model;

class MessageAction extends Model
{
    public $table = 'message_Action';

    public $fillable = [
        'conversation_id',
        'deleted_by',
    ];

    public static $rules = [
        'conversation_id' => 'required|integer',
        'deleted_by' => 'required|integer',
    ];
}
