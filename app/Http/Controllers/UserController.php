<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Queries\UserDataTable;
use App\Repositories\UserRepository;
use DataTables;
use Exception;
use Hash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Response;

class UserController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * @return Factory|View
     */
    public function getProfile()
    {
        return view('profile');
    }

    /**
     * Display a listing of the User.
     *
     * @param  Request  $request
     * @throws Exception
     * @return Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return Datatables::of((new UserDataTable())->get())->make(true);
        }
        $roles = Role::all()->pluck('name', 'id')->toArray();
        return view('users.index')->with([
                'roles' => $roles
            ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::all()->pluck('name', 'id')->toArray();
        return view('users.create')->with(['roles' => $roles]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  CreateUserRequest  $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $this->validateInput($request->all());

        $this->userRepository->store($input);

        return $this->sendSuccess('User saved successfully.');
    }

    /**
     * Display the specified User.
     * @param  User  $user
     *
     * @return Response
     */
    public function show(User $user)
    {
        $user = $user->apiObj();
        return view('users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  User  $user
     * @return Response
     */
    public function edit(User $user)
    {
        $user = $user->apiObj();
        return $this->sendResponse(['user' => $user], 'User retrieved successfully.');
    }

    /**
     * Update the specified User in storage.
     *
     * @param  User  $user
     * @param  UpdateUserRequest  $request
     *
     * @return Response
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        if ($user->is_system) {
            return $this->sendError('You can not update system generated user.');
        }

        $input = $this->validateInput($request->all());
        $this->userRepository->update($input, $user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  User  $user
     * @throws Exception
     *
     * @return Response
     */
    public function destroy(User $user)
    {
        if ($user->is_system) {
            return $this->sendError('You can not update system generated user.');
        }
        $this->userRepository->delete($user->id);

        return $this->sendSuccess('User deleted successfully.');
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     */
    public function activeDeActiveUser(User $user)
    {
        $this->userRepository->activeDeActiveUser($user->id);

        return $this->sendSuccess('User updated successfully.');
    }

    public function validateInput($input)
    {
        if (isset($input['password']) && ! empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $input['is_active'] = (!empty($input['is_active'])) ? 1 : 0;

        return $input;
    }
}
