<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use App\Services\Messages\MessagePublishingService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        /** @var \Illuminate\Database\Eloquent\Collection<int, Message> $allMessages */
        $allMessages = Message::query()
            ->with(['sender', 'receiver'])
            ->where(function (Builder $query) use ($currentUser): void {
                $query
                    ->where('sender_id', $currentUser->id)
                    ->orWhere('receiver_id', $currentUser->id);
            })
            ->latest()
            ->get();

        /** @var Collection<int, array{user: User, last_message: Message|null}> $contacts */
        $contacts = $allMessages
            ->toBase()
            ->map(function (Message $message) use ($currentUser): array {
                $contact = $message->sender_id === $currentUser->id
                    ? $message->receiver
                    : $message->sender;

                return [
                    'user' => $contact,
                    'last_message' => $message,
                ];
            })
            ->unique(static fn (array $contact): int => $contact['user']->id)
            ->values();

        $selectedUser = null;
        $selectedUserId = $request->integer('user');

        if ($selectedUserId > 0) {
            $selectedUser = User::query()->find($selectedUserId);

            if ($selectedUser?->is($currentUser) === true) {
                $selectedUser = null;
            }
        }

        if ($selectedUser === null && $contacts->isNotEmpty()) {
            /** @var array{user: User, last_message: Message|null}|null $firstContact */
            $firstContact = $contacts->first();
            $selectedUser = $firstContact['user'] ?? null;
        }

        if (
            $selectedUser !== null
            && ! $contacts->contains(static fn (array $contact): bool => $contact['user']->is($selectedUser))
        ) {
            $contacts = $contacts->prepend([
                'user' => $selectedUser,
                'last_message' => null,
            ])->values();
        }

        /** @var Collection<int, Message> $conversation */
        $conversation = collect();

        if ($selectedUser !== null) {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Message> $conversation */
            $conversation = Message::query()
                ->with(['sender', 'receiver'])
                ->where(function (Builder $query) use ($currentUser, $selectedUser): void {
                    $query
                        ->where(function (Builder $dialogQuery) use ($currentUser, $selectedUser): void {
                            $dialogQuery
                                ->where('sender_id', $currentUser->id)
                                ->where('receiver_id', $selectedUser->id);
                        })
                        ->orWhere(function (Builder $dialogQuery) use ($currentUser, $selectedUser): void {
                            $dialogQuery
                                ->where('sender_id', $selectedUser->id)
                                ->where('receiver_id', $currentUser->id);
                        });
                })
                ->oldest()
                ->get();
        }

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
