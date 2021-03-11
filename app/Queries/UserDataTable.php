<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Monika Vaghasiya
 * Email: monika.vaghasiya@infyom.com
 * Date: 11/13/2019
 * Time: 01:14 PM
 */

namespace App\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class UserDataTable.
 */
class UserDataTable
{
    /**
     * @return User[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
     */
    public function get()
    {
        $users = User::with(['roles'])->get()->except(getLoggedInUserId());
        foreach ($users as $key => $user) {
            /** @var User $user */
            $users[$key] = $user->apiObj();
        }

        return $users;
    }
}
