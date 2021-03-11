<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $last_seen
 * @property string|null $about
 * @property string $photo_url
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *     $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property int|null $is_online
 * @property string|null $activation_code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsOnline($value)
 * @property int|null $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsActive($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User role($roles, $guard = null)
 * @property-read string $role_id
 * @property-read string $role_name
 * @property int|null $is_system
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsSystem($value)
 * @property string|null $player_id One signal user id
 * @property int|null $is_subscribed
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereIsSubscribed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePlayerId($value)
 */
class User extends Authenticatable
{
    use Notifiable, ImageTrait, HasApiTokens, HasRoles;
    use ImageTrait {
        deleteImage as traitDeleteImage;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'last_seen',
        'is_online',
        'about',
        'photo_url',
        'activation_code',
        'is_active',
        'is_system',
        'email_verified_at',
        'player_id',
        'is_subscribed',
    ];

    static $PATH = 'users';
    const HEIGHT = 250;
    const WIDTH = 250;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

//    protected $appends = [ 'role_name', 'role_id' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'is_subscribed'     => 'boolean',
        'player_id'         => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name'    => 'required|string|max:100',
        'phone'   => 'nullable|integer',
        'role_id' => 'required|integer',
        //        'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
        'email'   => 'required|email|max:255|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
    ];

    public static $messages = [
        'phone.integer'    => 'Please enter valid phone number',
        'phone.digits'     => 'The phone number must be 10 digits long',
        'email.regex'      => 'Please enter valid email',
        'role_id.required' => 'Please select user role',
    ];

    /**
     * @param $value
     *
     * @return string
     */
    public function getPhotoUrlAttribute($value)
    {
        if (! empty($value)) {
            return $this->imageUrl(self::$PATH.DIRECTORY_SEPARATOR.$value);
        }

        return getUserImageInitial($this->id, $this->name);
    }

    /**
     * @return string
     */
    public function getRoleNameAttribute()
    {
        $userRoles = $this->roles->first();

        return (! empty($userRoles)) ? $userRoles->name : '';
    }

    /**
     * @return string
     */
    public function getRoleIdAttribute()
    {
        $userRoles = $this->roles->first();

        return (! empty($userRoles)) ? $userRoles->id : '';
    }

    /**
     * @return array
     */
    public function webObj()
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'last_seen' => $this->last_seen,
            'about'     => $this->about,
            'photo_url' => $this->photo_url,
        ];
    }

    /**
     * @return array
     */
    public function apiObj()
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => (! empty($this->email_verified_at)) ? $this->email_verified_at->toDateTimeString() : '',
            'phone'             => $this->phone,
            'last_seen'         => $this->last_seen,
            'is_online'         => $this->is_online,
            'is_active'         => $this->is_active,
            'about'             => $this->about,
            'photo_url'         => $this->photo_url,
            'activation_code'   => $this->activation_code,
            'created_at'        => (! empty($this->created_at)) ? $this->created_at->toDateTimeString() : '',
            'updated_at'        => (! empty($this->updated_at)) ? $this->updated_at->toDateTimeString() : '',
            'is_system'         => $this->is_system,
            'role_name'         => $this->role_name,
            'role_id'           => $this->role_id,
        ];
    }

    /**
     * @return bool
     */
    public function deleteImage()
    {
        $image = $this->getOriginal('photo_url');
        if (empty($image)) {
            return true;
        }

        return $this->traitDeleteImage(self::$PATH.DIRECTORY_SEPARATOR.$image);
    }
}
