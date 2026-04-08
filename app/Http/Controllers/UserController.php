<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\Auth\LoginUserService;
use App\Services\Auth\RegisterUserService;
use App\Services\Posts\PostViewService;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show(Request $request, User $user, PostViewService $postViewService): View|JsonResponse
    {
        $tab = $this->resolveTab($request->query('tab'));
        /** @var User|null $viewer */
        $viewer = $request->user();

        $user->loadCount(['followers', 'posts']);

        $posts = null;
        $subscriptions = null;

        if ($tab === 'likes') {
            $posts = $postViewService->likedByUser($user, $viewer);
        } elseif ($tab === 'subscriptions') {
            $subscriptions = $user->followedUsers()
                ->withCount(['followers', 'posts'])
                ->paginate(10)
                ->withQueryString();
        } else {
            $posts = $postViewService->forUser($user, $viewer);
        }

        $isSubscribed = $viewer !== null
            && ! $viewer->is($user)
            && $viewer->followedUsers()->whereKey($user->id)->get()->isNotEmpty();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'user' => $user,
                'tab' => $tab,
                'posts' => $posts,
                'subscriptions' => $subscriptions,
                'is_subscribed' => $isSubscribed,
            ]);
        }

        return view('pages.user-show', [
            'profileUser' => $user,
            'activeTab' => $tab,
            'posts' => $posts,
            'subscriptions' => $subscriptions,
            'isSubscribed' => $isSubscribed,
        ]);
    }

    public function profile(Request $request): RedirectResponse
    {
        return redirect()->route('users.show', [
            'user' => $request->user(),
            'tab' => $this->resolveTab($request->query('tab')),
        ]);
    }

    public function login(LoginUserRequest $request, LoginUserService $loginUserService): RedirectResponse|JsonResponse
    {
        $loginUserService->authenticate($request->validated());

        if ($request->expectsJson() || $request->is('api/*')) {
            /** @var User $user */
            $user = Auth::user();

            return response()->json([
                'token' => $user->createToken('api')->plainTextToken,
                'user' => $user,
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('main'));
    }

    public function register(
        StoreUserRequest $request,
        RegisterUserService $registerUserService,
    ): RedirectResponse|JsonResponse {
        $user = $registerUserService->register($request->validated(), $request->file('avatar'));

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $user,
            ], 201);
        }

        return redirect()->route('login')
            ->with('status', 'Аккаунт успешно зарегистрирован. Теперь войдите в систему.');
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $request->user()?->currentAccessToken()?->delete();

            return response()->json([], 204);
        }

        /** @var StatefulGuard $guard */
        $guard = Auth::guard('web');
        $guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function resolveTab(mixed $tab): string
    {
        if (! is_string($tab)) {
            return 'posts';
        }

        $normalized = Str::lower($tab);

        return in_array($normalized, ['posts', 'likes', 'subscriptions'], true)
            ? $normalized
            : 'posts';
    }
}
