<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\User;
use App\Services\Messages\MessagePublishingService;
use App\Services\Messages\MessageThreadService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, MessageThreadService $messageThreadService): JsonResponse|View
    {
        /** @var User $currentUser */
        $currentUser = $request->user();
        $thread = $messageThreadService->build($currentUser, $request->integer('user'));
        $contacts = $thread['contacts'];
        $selectedUser = $thread['selected_user'];
        $conversation = $thread['conversation'];

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'contacts' => $contacts,
                'selected_user' => $selectedUser,
                'conversation' => $conversation,
            ]);
        }

        return view('pages.messages', [
            'contacts' => $contacts,
            'selectedUser' => $selectedUser,
            'conversation' => $conversation,
        ]);
    }

    public function store(
        StoreMessageRequest $request,
        MessagePublishingService $messagePublishingService,
    ): JsonResponse|RedirectResponse {
        /** @var User $sender */
        $sender = $request->user();
        /** @var User $receiver */
        $receiver = User::query()->findOrFail((int) $request->validated('receiver_id'));

        $message = $messagePublishingService->send(
            $sender,
            $receiver,
            (string) $request->validated('content'),
        );

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $message,
            ], 201);
        }

        return redirect()->route('messages', ['user' => $receiver->id]);
    }
}
