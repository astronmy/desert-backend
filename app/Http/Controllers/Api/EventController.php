<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));

        $events = Event::query()
            ->with('images')
            ->orderByDesc('end_date')
            ->paginate($perPage);

        return EventResource::collection($events);
    }
}
