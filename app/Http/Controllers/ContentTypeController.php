<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Http\Requests\StoreContentTypeRequest;
use App\Http\Requests\UpdateContentTypeRequest;

class ContentTypeController extends Controller
{
    public function index()
    {
        return ContentType::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'icon_class' => 'required|string',
        ]);

        return ContentType::create($data);
    }

    public function update(Request $request, ContentType $contentType)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'icon_class' => 'required|string',
        ]);

        $contentType->update($data);

        return $contentType;
    }

    public function destroy(ContentType $contentType)
    {
        $contentType->delete();
        return response()->noContent();
    }
}
