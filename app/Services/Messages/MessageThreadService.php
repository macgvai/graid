<?php

namespace App\Services\Messages;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MessageThreadService
{
    /**
     * @return array{
     *     contacts: Collection<int, array{user: User, last_message: Message|null}>,
     *     selected_user: User|null,
     *     conversation: Collection<int, Message>
     * }
     */
    public function build(User $currentUser, ?int $selectedUserId = null): array
    {
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

        $selectedUser = $this->resolveSelectedUser($currentUser, $contacts, $selectedUserId);

        if (
            $selectedUser !== null
            && ! $contacts->contains(static fn (array $contact): bool => $contact['user']->is($selectedUser))
        ) {
            $contacts = $contacts->prepend([
                'user' => $selectedUser,
                'last_message' => null,
            ])->values();
        }

        return [
            'contacts' => $contacts,
            'selected_user' => $selectedUser,
            'conversation' => $this->loadConversation($currentUser, $selectedUser),
        ];
    }

    /**
     * @param  Collection<int, array{user: User, last_message: Message|null}>  $contacts
     */
    private function resolveSelectedUser(User $currentUser, Collection $contacts, ?int $selectedUserId): ?User
    {
        $selectedUser = null;

        if ($selectedUserId !== null && $selectedUserId > 0) {
            /** @var User|null $selectedUser */
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

        return $selectedUser;
    }

    /**
     * @return Collection<int, Message>
     */
    private function loadConversation(User $currentUser, ?User $selectedUser): Collection
    {
        if ($selectedUser === null) {
            return collect();
        }

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

        return $conversation;
    }
}
