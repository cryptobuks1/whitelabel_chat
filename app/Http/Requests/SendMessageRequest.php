<?php

namespace App\Http\Requests;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use OneSignal;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Conversation::$rules;
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['message'] = htmlspecialchars($input['message']);
        $input['to_id'] = htmlspecialchars($input['to_id']);

        $toUser = User::findOrFail($input['to_id']);
        if (! is_null($toUser->player_id) && $toUser->is_subscribed) {
            $oneSignalPlayerId = $toUser->player_id;
            OneSignal::setParam('heading', 'Message Received')->sendNotificationToUser(
                $input['message'],
                $oneSignalPlayerId,
                null,
                null,
                null,
                null,
                $toUser->name . ' | '.config('app.name')
            );
        }

        $this->replace($input);
    }

}
