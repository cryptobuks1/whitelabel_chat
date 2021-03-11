<?php

namespace App\Repositories;

use App\Events\ConversationEvent;
use App\Exceptions\ApiOperationFailedException;
use App\Models\MessageAction;
use App\Models\Conversation;
use App\Models\User;
use App\Traits\ImageTrait;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ChatRepository
 */
class ChatRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'from_id', 'to_id', 'message', 'status', 'file_name',
    ];

    /**
     * Return searchable fields.
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model.
     **/
    public function model()
    {
        return Conversation::class;
    }

    /**
     * @return Conversation|Collection
     */
    public function getLatestConversations()
    {
        $authId = getLoggedInUserId();

        $subQuery = Conversation::leftJoin('users as u', 'u.id', '=', DB::raw("if(from_id = $authId, to_id, from_id)"))
            ->leftJoin('message_action as ma', function (JoinClause $join) use ($authId) {
                $join->on('ma.deleted_by', '=', DB::raw("$authId"));
                $join->on('ma.conversation_id', '=', 'conversations.id');
            })
            ->where(function (Builder $q) use ($authId) {
                $q->where('from_id', '=', $authId)->orWhere('to_id', '=', $authId);
            })
            ->where(function (Builder $q) {
                $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                    ->orWhereNull('ma.conversation_id');
            })
            ->selectRaw(
                "max(conversations.id) as latest_id , u.id as user_id,
                 sum(if(conversations.status = 0 and from_id != $authId, 1, 0)) as unread_count"
            )
            ->groupBy(DB::raw("if(from_id = $authId, to_id, from_id)"))
            ->orderBy('conversations.created_at', 'desc');

        $subQueryStr = $subQuery->toSql();

        $chatList = Conversation::with(['user'])->newQuery();
        $chatList = $chatList->select("temp.*", "cc.*")
            ->from(DB::raw("($subQueryStr) as temp"))
            ->setBindings($subQuery->getBindings())
            ->leftJoin("conversations as cc", 'cc.id', '=', 'temp.latest_id')
            ->orderBy("cc.created_at",'desc')
            ->get();

        return $chatList;
    }

    /**
     * @param  array  $input
     *
     * @return Conversation
     */
    public function sendMessage($input)
    {
        $input['from_id'] = getLoggedInUserId();
        if (isValidURL($input['message'])) {
            $input['message_type'] = detectURL($input['message']);
        }
        /** @var $conversation Conversation */
        $conversation = $this->create($input);
        $conversation->sender;
        $conversation->receiver;

        broadcast(new ConversationEvent($conversation, $conversation->to_id))->toOthers();

        return $conversation;
    }

    /**
     * @param  UploadedFile  $file
     *
     * @throws ApiOperationFailedException
     *
     * @return string|void
     */
    public function addAttachment($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension,
            ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'jpg', 'gif', 'jpeg', 'png', 'mp4', 'mkv', 'avi', 'txt', 'mp3', 'ogg', 'wav', 'aac', 'alac'])) {
            throw new ApiOperationFailedException('You can not upload this file.', Response::HTTP_BAD_REQUEST);
        }

        if (in_array($extension, ['jpg', 'gif', 'png', 'jpeg'])) {
            $fileName = ImageTrait::makeImage($file, Conversation::PATH, []);

            return $fileName;
        }

        if (in_array($extension, ['xls', 'pdf', 'doc', 'docx', 'xlsx', 'txt'])) {
            $fileName = ImageTrait::makeAttachment($file, Conversation::PATH);

            return $fileName;
        }

        if (in_array($extension, ['mp4', 'mkv', 'avi'])) {
            $fileName = ImageTrait::uploadVideo($file, Conversation::PATH);

            return $fileName;
        }

        if (in_array($extension, ['mp3', 'ogg', 'wav', 'aac', 'alac'])) {
            $fileName = ImageTrait::uploadFile($file, Conversation::PATH);

            return $fileName;
        }
    }

    /**
     * @param $extension
     *
     * @return int
     */
    public function getMessageTypeByExtension($extension)
    {
        $extension = strtolower($extension);
        if (in_array($extension, ['jpg', 'gif', 'png', 'jpeg'])) {
            return Conversation::MEDIA_IMAGE;
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return Conversation::MEDIA_DOC;
        } elseif ($extension == 'pdf') {
            return Conversation::MEDIA_PDF;
        } elseif (in_array($extension, ['mp3', 'ogg', 'wav', 'aac', 'alac'])) {
            return Conversation::MEDIA_VOICE;
        } elseif (in_array($extension, ['mp4', 'mkv', 'avi'])) {
            return Conversation::MEDIA_VIDEO;
        } elseif (in_array($extension, ['txt'])) {
            return Conversation::MEDIA_TXT;
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return Conversation::MEDIA_XLS;
        } else {
            return 0;
        }
    }

    /**
     * @param $input
     *
     * @return array
     */
    public function markMessagesAsRead($input)
    {
        $senderId = 0;
        $remainingUnread = 0;
        if (isset($input['ids']) && !empty($input['ids'])) {
            $unreadIds = $input['ids'];
            $unreadIds = (is_array($unreadIds)) ? $unreadIds : [$unreadIds];
            $firstUnreadConversationId = $unreadIds[0];
            Conversation::whereIn('id', $unreadIds)->update(['status' => 1]);

            $conversation = Conversation::find($firstUnreadConversationId);
            $senderId = ($conversation->from_id == getLoggedInUserId()) ? $conversation->to_id : $conversation->from_id;
            $remainingUnread = $this->getUnreadMessageCount($senderId);
        }
        return ['senderId' => $senderId, 'remainingUnread' => $remainingUnread];
    }

    /**
     * @param $senderId
     *
     * @return int
     */
    public function getUnreadMessageCount($senderId) {
        return Conversation::where(function (Builder $q) use ($senderId) {
            $q->where('from_id', '=', $senderId)->where('to_id', '=', getLoggedInUserId());
        })->where('status', '=', 0)->count();
    }

    /**
     * @param $userId
     */
    public function deleteConversation($userId) {
        $chatIds = Conversation::leftJoin('message_action as ma', function (JoinClause $join) {
            $authUserId = getLoggedInUserId();
                $join->on('ma.deleted_by', '=', DB::raw("$authUserId"));
                $join->on('ma.conversation_id', '=', 'conversations.id');
            })
            ->where(function (Builder $q) use ($userId) {
                $q->where(function (Builder $q) use ($userId) {
                    $q->where('from_id', '=', $userId)
                        ->where('to_id', '=', getLoggedInUserId());
                })->orWhere(function (Builder $q) use ($userId) {
                    $q->where('from_id', '=', getLoggedInUserId())
                        ->where('to_id', '=', $userId);
                });
            })
            ->where(function (Builder $q) {
                $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                    ->orWhereNull('ma.conversation_id');
            })
            ->get(['conversations.*'])
            ->pluck('id')
            ->toArray();

        $input = [];
        foreach ($chatIds as $chatId) {
            $input[] = [
                'conversation_id' => $chatId,
                'deleted_by' => getLoggedInUserId()
            ];
        }

        MessageAction::insert($input);
    }

}
