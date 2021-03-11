<?php

namespace App\Repositories;

use App;
use App\Exceptions\ApiOperationFailedException;
use App\Models\Conversation;
use App\Models\User;
use App\Traits\ImageTrait;
use Auth;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name', 'email', 'phone', 'last_seen', 'about', 'photo_url', 'is_online',
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
        return User::class;
    }

    /**
     * @param  array  $input
     *
     * @return User
     */
    public function store($input)
    {
        /** @var AccountRepository $accountRepo */
        $accountRepo = App::make(AccountRepository::class);

        try {
            /** @var User $user */
            $user = User::create($input);
            $this->assignRoles($user, $input);
            $this->updateProfilePhoto($user, $input);

            $activateCode = $accountRepo->generateUserActivationToken($user->id);
            if (! $user->is_active) {
                $accountRepo->sendConfirmEmail($user->name, $user->email, $activateCode);
            }

            return $user;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  array  $input
     * @param  int  $id
     *
     * @return User
     */
    public function update($input, $id)
    {
        /** @var User $user */
        $user = User::findOrFail($id);
        try {
            $user->update($input);

            $this->assignRoles($user, $input);
            $this->updateProfilePhoto($user, $input);

            return $user;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param  int  $id
     * @param  array  $input
     *
     * @return array
     */
    public function getConversation($id, $input)
    {
        /** @var User $user */
        $orderBy = 'desc';
        $user = $this->find($id);
        $authUser = getLoggedInUser();
        $limit = Conversation::LIMIT;
//        $limit = (isset($input['limit']) && !empty($input['limit'])) ? $input['limit'] : Conversation::LIMIT;

        if (empty($user)) {
            throw new BadRequestHttpException('User not found.');
        }
        if (isset($input['before']) && empty($input['before'])) {
            return ['user' => $user, 'conversations' => []];
        }
        if (isset($input['after']) && empty($input['after'])) {
            return ['user' => $user, 'conversations' => []];
        }
        $query = Conversation::with(['sender', 'receiver'])
            ->leftJoin('message_action as ma', function (JoinClause $join) use ($authUser) {
                $join->on('ma.deleted_by', '=', DB::raw("$authUser->id"));
                $join->on('ma.conversation_id', '=', 'conversations.id');
            })
            ->where(function (Builder $q) {
                $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                    ->orWhereNull('ma.conversation_id');
            })
            ->where(function (Builder $q) use ($authUser, $id) {
                $q->where(function (Builder $q) use ($authUser, $id) {
                    $q->where('from_id', '=', $authUser->id)->where('to_id', '=', $id);
                })->orWhere(function (Builder $q) use ($authUser, $id) {
                    $q->where('from_id', '=', $id)->where('to_id', '=', $authUser->id);
                });
            });

        $countQuery = clone $query;
        $unreadCount = $countQuery->where('status', '=', 0)->where('to_id', '=', getLoggedInUserId())->count();
        $needToReverse = false;

        if (isset($input['before']) && ! empty($input['before'])) {
            $query->where('conversations.id', '<', $input['before']);
        } elseif (isset($input['after']) && ! empty($input['after'])) {
            $query->where('conversations.id', '>', $input['after']);
            $orderBy = 'ASC';
        } elseif ($unreadCount > $limit) {
            $query->where('status', '=', 0);
            $orderBy = 'ASC';
            $needToReverse = true;
        }

        $query->limit($limit);
        $query->orderBy('conversations.id', $orderBy);
        $messages = $query->get(['conversations.*'])->toArray();
        $messages = ($needToReverse) ? array_reverse($messages) : $messages;

        return ['user' => $user, 'conversations' => $messages];
    }

    /**
     * @param  array  $input
     *
     * @throws ApiOperationFailedException
     *
     * @return bool|void
     */
    public function updateProfile($input)
    {
        /** @var User $user */
        $user = Auth::user();
        if (empty($user)) {
            throw new BadRequestHttpException('User not found.');
        }

        $this->updateProfilePhoto($user, $input);

        return true;
    }

    public function updateProfilePhoto(User $user, $input)
    {
        try {
            $options = ['height' => User::HEIGHT, 'width' => User::WIDTH];
            if (! empty($input['photo'])) {
                $input['photo_url'] = ImageTrait::makeImage($input['photo'], User::$PATH, $options);

                $oldImageName = $user->photo_url;
                if (! empty($oldImageName)) {
                    $user->deleteImage();
                }
            }

            $user->update($input);
        } catch (Exception $e) {
            throw new ApiOperationFailedException($e->getMessage());
        }
    }

    /**
     * @param $user
     * @param $input
     *
     * @return bool
     */
    public function assignRoles($user, $input)
    {
        $roles = ! empty($input['role_id']) ? $input['role_id'] : [];
        /** @var User $user */
        $user->roles()->sync($roles);

        return true;
    }

    /**
     * @param  int  $id
     *
     * @return User
     */
    public function activeDeActiveUser($id)
    {
        /** @var User $user */
        $user = $this->find($id);
        $user->is_active = ! $user->is_active;
        $user->save();

        return $user;
    }

    /**
     * @param  array  $input
     *
     * @return bool
     */
    public function storeAndUpdateNotification($input)
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update($input);

        return true;
    }
}
