<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Traits\Res;
use Illuminate\Http\JsonResponse;
use Modules\SystemSetting\app\Http\Requests\SendMessageRequest;
use Modules\SystemSetting\app\Http\Resources\Api\MessageResource;
use Modules\SystemSetting\app\Services\Api\MessageApiService;

class MessageApiController
{
    use Res;

    protected $messageService;

    public function __construct(MessageApiService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Send a message to a user or guest
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $message = $this->messageService->createMessage($request->validated());

        return $this->sendRes(
            __('system-setting::messages.send_success'),
            true,
            new MessageResource($message),
            [],
            201
        );
    }
}
