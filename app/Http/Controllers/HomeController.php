<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        /** @var User|null $viewer */
        $viewer = $request->user();

        if ($viewer === null) {
            return view('pages.main', [
                'posts' => null,
            ]);
        }

        return redirect()->route('feed');
    }
}
