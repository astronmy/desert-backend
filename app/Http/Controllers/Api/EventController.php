<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));
        $today = Carbon::today()->toDateString();

        $events = Event::query()
            ->with('images')
            ->orderByRaw('CASE WHEN end_date >= ? THEN 0 ELSE 1 END', [$today])
            ->orderByRaw('CASE WHEN end_date >= ? THEN end_date END ASC', [$today])
            ->orderByRaw('CASE WHEN end_date < ? THEN end_date END DESC', [$today])
            ->paginate($perPage);

        return EventResource::collection($events);
    }
}
