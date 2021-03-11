<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Role
 *
 * @package App\Models
 * @version November 12, 2019, 11:13 am UTC
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $is_default
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereIsDefault($value)
 */
class Role extends Model
{
    public $table = 'roles';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'name',
        'guard_name',
        'is_default'
    ];

    const ADMIN_ROLE = 1;
    const MEMBER_ROLE = 2;

    const MEMBER_ROLE_NAME = 'Member';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
    ];

}
