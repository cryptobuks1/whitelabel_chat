<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Carbon\Carbon;
use Crypt;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laravel\Passport\Bridge\AccessToken;
use Session;

/**
 * Class AuthController.
 */
class AuthController extends AppBaseController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Redirector|View
     */
    public function verifyAccount(Request $request)
    {
        $token = $request->get('token', null);

        if (empty($token)) {
            Session::flash('error', 'token not found');

            return redirect('login');
        }

        try {
            $token = Crypt::decrypt($token);
            list($userId, $activationCode) = $result = explode('|', $token);

            if (count($result) < 2) {
                Session::flash('error', 'token not found');

                return redirect('login');
            }

            /** @var User $user */
            $user = User::whereActivationCode($activationCode)->findOrFail(
                $userId
            );

            if (empty($user)) {
                Session::flash(
                    'msg',
                    'This account activation token is invalid'
                );

                return redirect('login');
            }
            if ($user->is_active) {
                Session::flash(
                    'success',
                    'Your account already activated. Please do a login'
                );

                return redirect('login');
            }

            $user->is_active = true;
            $user->email_verified_at = Carbon::now();
            $user->save();

            Session::flash(
                'success',
                'Your account is successfully activated. Please do a login'
            );

            return redirect('login');
        } catch (Exception $e) {
            Session::flash('msg', 'Something went wrong');

            return redirect('login');
        }
    }

    public function autoLogin(Request $request)
    {
        $user_id = $request->get('user_id');
        $key = $request->get('key');
        $user_type = $request->get('user_type');

        if ($user_id != null && $key != null && $user_type != null) {
            if ($key == env('MAIN_APP_KEY')) {
                $user = User::where('model_id', $user_id)
                    ->where('user_type', $user_type)
                    ->first();
                if ($user) {
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    $token->save();

                    $user->update(['is_online' => 1, 'last_seen' => null]);
                    $access_token = $tokenResult->accessToken;
                    return response()->json(['token' => $access_token], 200);
                } else {
                    return response()->json(['error' => 'No such user present! Please contact technical helpdesk.'], 404);
                }
            } else {
                return response()->json(['error' => 'Secret Key does not match! Please contact technical helpdesk.'], 404);
            }
        } else {
            return response()->json(['error' => 'Please provide all parameters: user id, user type and the secret key!'], 404);
        }
    }

    public function setKey(Request $request)
    {
        $access_token = $request->get('token');
        return view('auth.redirect', compact('access_token'));
    }
}
