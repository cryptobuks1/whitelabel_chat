<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateUserNotificationRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Auth;
use Carbon\Carbon;
use Exception;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserAPIController
 */
class UserAPIController extends AppBaseController
{
    /** @var UserRepository */
    private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return JsonResponse
     */
    public function getUsersList()
    {
        $users = User::orderBy('name','asc')->get()->except(getLoggedInUserId());

        return $this->sendResponse(['users' => $users], 'Users retrieved successfully.');
    }

    /**
     * @return JsonResponse
     */
    public function getProfile() {
        /** @var User $authUser **/
        $authUser = getLoggedInUser();
        $authUser = $authUser->apiObj();
        return $this->sendResponse(['user' => $authUser], 'Users retrieved successfully.');
    }

    /**
     * @param  UpdateUserProfileRequest  $request
     *
     * @return JsonResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {
            $this->userRepository->updateProfile($request->all());

            return $this->sendSuccess('Profile updated successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function updateLastSeen(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $lastSeen = ($request->has('status') && $request->get('status') > 0) ? null : Carbon::now();

        $user->update(['last_seen' => $lastSeen, 'is_online' => $request->get('status')]);

        return $this->sendResponse(['user' => $user], 'Last seen updated successfully.');
    }

    /**
     * @param  int  $id
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getConversation($id, Request $request)
    {
        $input = $request->all();
        $data = $this->userRepository->getConversation($id, $input);

        return $this->sendResponse($data, 'Conversation retrieved successfully.');
    }

    /**
     * @param  ChangePasswordRequest  $request
     *
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        $user->update(['password' => $input['password']]);

        return $this->sendSuccess('Password updated successfully.');
    }

    /**
     * @param  UpdateUserNotificationRequest  $request
     *
     * @return JsonResponse
     */
    public function updateNotification(UpdateUserNotificationRequest $request)
    {
        $input = $request->all();
        $input['is_subscribed'] = ($input['is_subscribed'] == 'true') ? true : false;

        $this->userRepository->storeAndUpdateNotification($input);

        return $this->sendSuccess('Notification updated successfully.');
    }
}
