<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Eloquent as Model;

/**
 * App\Models\Conversation
 *
 * @property int $id
 * @property int|null $from_id
 * @property int|null $to_id
 * @property string $message
 * @property int $status 0 for unread,1 for seen
 * @property int $message_type 0- text message, 1- image, 2- pdf, 3- doc, 4- voice
 * @property string|null $file_name
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $photo_url
 * @property-read \App\Models\User|null $receiver
 * @property-read \App\Models\User|null $sender
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Conversation withoutTrashed()
 */
class Conversation extends Model
{
    use ImageTrait;
    public $table = 'conversations';

    public $fillable = [
        'from_id',
        'to_id',
        'message',
        'message_type',
        'status',
        'file_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'from_id' => 'integer',
        'to_id' => 'integer',
        'message' => 'string',
        'message_type' => 'integer',
        'file_name' => 'string',
    ];

    public static $rules = [
        'to_id' => 'required|integer',
        'message' => 'required|string',
    ];

    const LIMIT = 100;
    const PATH = 'conversation';
    const MEDIA_IMAGE = 1;
    const MEDIA_PDF = 2;
    const MEDIA_DOC = 3;
    const MEDIA_VOICE = 4;
    const MEDIA_VIDEO = 5;
    const YOUTUBE_URL = 6;
    const MEDIA_TXT = 7;
    const MEDIA_XLS = 8;


    public function getMessageAttribute($value)
    {
        if (!in_array($this->message_type, [0, 6])) {
            return asset('uploads/conversation').DIRECTORY_SEPARATOR.$value;
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getPhotoUrlAttribute($value)
    {
        if (!empty($value)) {
            return $this->imageUrl(User::$PATH . DIRECTORY_SEPARATOR . $value);
        }

        return asset('assets/images/avatar.png');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
