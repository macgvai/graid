<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Models\User;
use App\Services\Subscriptions\SubscribeToUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function store(
        StoreSubscriptionRequest $request,
        User $user,
        SubscribeToUserService $subscribeToUserService,
    ): JsonResponse|RedirectResponse {
        /** @var User $subscriber */
        $subscriber = $request->user();
        $subscription = $subscribeToUserService->subscribe($subscriber, $user);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $subscription,
            ], 201);
        }

        return redirect()->route('users.show', $user);
    }

    public function destroy(
        Request $request,
        User $user,
        SubscribeToUserService $subscribeToUserService,
    ): JsonResponse|RedirectResponse {
        /** @var User $subscriber */
        $subscriber = $request->user();
        $subscribeToUserService->unsubscribe($subscriber, $user);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([], 204);
        }

        return redirect()->route('users.show', $user);
    }
}
