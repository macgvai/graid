<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'author_id' => 'required|exists:users,id',
            'target_id' => 'required|exists:users,id',
        ]);

        if ($data['author_id'] === $data['target_id']) {
            return response()->json(['error' => 'Нельзя подписаться на себя'], 400);
        }

        return Subscription::firstOrCreate($data);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->noContent();
    }
}
