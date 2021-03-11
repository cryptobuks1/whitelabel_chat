<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Repositories\RoleRepository;
use Exception;
use Flash;
use Response;

class RoleController extends AppBaseController
{
    /** @var  RoleRepository */
    private $roleRepository;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepository = $roleRepo;
    }

    /**
     * Display a listing of the Role.
     *
     * @return Response
     */
    public function index()
    {
        $roles = $this->roleRepository->all();

        return view('roles.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return Response
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();

        $this->roleRepository->create($input);

        return $this->sendSuccess('Role saved successfully.');
    }

    /**
     * Display the specified Role.
     *
     * @param  Role  $role
     * @return Response
     */
    public function show(Role $role)
    {
        return $this->sendResponse($role, 'Role retrieved successfully');
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param  Role  $role
     * @return Response
     */
    public function edit(Role $role)
    {
        return $this->sendResponse($role, 'Role retrieved successfully');
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  Role  $role
     * @param  UpdateRoleRequest  $request
     *
     * @return Response
     */
    public function update(Role $role, UpdateRoleRequest $request)
    {
        if ($role->is_default) {
            Flash::success('You can not edit default role.');

            return redirect(route('roles.index'));
        }
        $this->roleRepository->update($request->all(), $role->id);

        Flash::success('Role updated successfully.');

        return $this->sendSuccess('Role updated successfully.');
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  Role  $role
     * @throws Exception
     * @return Response
     */
    public function destroy(Role $role)
    {
        if ($role->is_default) {
            Flash::success('You can not delete default role.');

            return redirect(route('roles.index'));
        }
        $this->roleRepository->delete($role->id);

        Flash::success('Role deleted successfully.');

        return redirect(route('roles.index'));
    }
}
