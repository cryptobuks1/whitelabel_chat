<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ApiOperationFailedException;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\SendMessageRequest;
use App\Repositories\ChatRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ChatAPIController
 */
class ChatAPIController extends AppBaseController
{
    /** @var ChatRepository */
    private $chatRepository;

    /**
     * Create a new controller instance.
     *
     * @param  ChatRepository  $chatRepository
     */
    public function __construct(ChatRepository $chatRepository)
    {
        $this->chatRepository = $chatRepository;
    }

    /**
     * This function return latest conversations of users.
     *
     * @return JsonResponse
     */
    public function getLatestConversations()
    {
        $conversations = $this->chatRepository->getLatestConversations();

        return $this->sendResponse(['conversations' => $conversations], 'Conversations retrieved successfully.');
    }

    /**
     * @param  SendMessageRequest  $request
     *
     * @return JsonResponse
     */
    public function sendMessage(SendMessageRequest $request)
    {
        $conversation = $this->chatRepository->sendMessage($request->all());

        return $this->sendResponse(['message' => $conversation], 'Message sent successfully.');
    }

    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function updateConversationStatus(Request $request)
    {
        $data = $this->chatRepository->markMessagesAsRead($request->all());

        return $this->sendResponse($data, 'Status updated successfully.');
    }

    /**
     * @param  Request  $request
     *
     * @throws ApiOperationFailedException
     *
     * @return JsonResponse
     */
    public function addAttachment(Request $request)
    {
        $files = $request->file('file');
        foreach ($files as $file) {
            $fileData['attachment'] = $this->chatRepository->addAttachment($file);
            $extension = $file->getClientOriginalExtension();
            $fileData['message_type'] = $this->chatRepository->getMessageTypeByExtension($extension);
            $fileData['file_name'] = $file->getClientOriginalName();
            $fileData['unique_code'] = uniqid();
            $data['data'][] = $fileData;
        }
        $data['success'] = true;

        return $this->sendData($data);
    }

    /**
     * @param $userId
     *
     * @return JsonResponse
     */
    public function deleteConversation($userId) {
        $this->chatRepository->deleteConversation($userId);

        return $this->sendSuccess('Conversation deleted successfully.');
    }
}
