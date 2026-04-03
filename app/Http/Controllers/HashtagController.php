<?php

namespace App\Http\Controllers;

use App\Models\Hashtag;
use App\Http\Requests\StoreHashtagRequest;
use App\Http\Requests\UpdateHashtagRequest;

class HashtagController extends Controller
{
    public function index()
    {
        return Hashtag::paginate(50);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:hashtags,name',
        ]);

        return Hashtag::create($data);
    }

    public function destroy(Hashtag $hashtag)
    {
        $hashtag->delete();
        return response()->noContent();
    }
}
