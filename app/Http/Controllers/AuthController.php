<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Crypt;
use DB;
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

        if ($user_id == null || $key == null || $user_type == null) {
            return response()->json(['error' => 'Please provide all parameters: user id, user type and the secret key!'], 404);
        }
        if ($key != '12551a11s54d22awerws21s33564sdf21s21f2s1d22e') {
            return response()->json(['error' => 'Secret Key does not match! Please contact technical helpdesk.'], 404);
        }
        $chat_user = User::where('model_id', $user_id)->where('user_type', $user_type)->first();

        if ($user_type == 'creditor') {
            $user = DB::table('users')->where('id', $user_id)->first();
        } elseif ($user_type == 'consumer') {
            $user = Consumer::find($user_id);           
        } else {
            $user = null;
        }
        if ($user == null) {
            return response()->json(['error' => "$user_type with id: $user_id could not be found"], 404);
        }
        if (!$chat_user) {

            $chat_user = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                //'phone' => $user->phone_no,
                'last_seen' => now(),
                'is_online' => true,
                'about' => null,
                'photo_url' => null,
                'activation_code' => null,
                'is_active' => true,
                'is_system' => false,
                'email_verified_at' => $user->email_verified_at,
                'model_id' => $user->id,
                'user_type' => $user_type,
                'company_ids' => $user->company_id
            ]);
        }
        
        $tokenResult = $chat_user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        $chat_user->update(['is_online' => 1, 'last_seen' => null]);
        $access_token = $tokenResult->accessToken;
        return response()->json(['token' => $access_token], 200);
    }

    public function setKey(Request $request)
    {
        $access_token = $request->get('token');
        return view('auth.redirect', compact('access_token'));
    }
}
