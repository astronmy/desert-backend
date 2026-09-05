<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventPlace;
use App\Enums\EventType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\StoreEventRequest;
use App\Http\Requests\Admin\Event\UpdateEventRequest;
use App\Models\Event;
use App\Services\Deeplink\EventRegistrationLinkService;
use App\Services\Events\PersistEventMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly PersistEventMediaService $persistEventMedia
    ) {}

    public function index(Request $request): View
    {
        $events = Event::query()
            ->when($request->filled('name'), fn ($q) => $q->where('name', 'like', '%'.$request->string('name').'%'))
            ->when($request->filled('host'), fn ($q) => $q->where('host', 'like', '%'.$request->string('host').'%'))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('place'), fn ($q) => $q->where('place', $request->string('place')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('init_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('init_date', '<=', $request->date('date_to')))
            ->orderByDesc('init_date')
            ->paginate(15)
            ->withQueryString();

        $types = EventType::options();
        $places = EventPlace::options();

        return view('admin.events.index', compact('events', 'types', 'places'));
    }

    public function create(): View
    {
        $types = EventType::options();
        $places = EventPlace::options();

        return view('admin.events.create', compact('types', 'places'));
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->safe()->only([
            'name',
            'init_date',
            'end_date',
            'type',
            'place',
            'description',
            'short_description',
            'host',
        ]);

        $this->persistEventMedia->create($data, [
            'image' => $request->file('image'),
            'mobile_image' => $request->file('mobile_image'),
            'gallery' => $request->file('gallery', []),
        ]);

        return redirect()->route('admin.events.index')
            ->with('status', __('event.messages.created'));
    }

    public function edit(Event $event): View
    {
        $event->load('images');
        $types = EventType::options();
        $places = EventPlace::options();

        return view('admin.events.edit', compact('event', 'types', 'places'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->safe()->only([
            'name',
            'init_date',
            'end_date',
            'type',
            'place',
            'description',
            'short_description',
            'host',
        ]);

        $this->persistEventMedia->update($event, $data, [
            'image' => $request->file('image'),
            'mobile_image' => $request->file('mobile_image'),
            'gallery' => $request->file('gallery', []),
            'remove_image' => $request->boolean('remove_image'),
            'remove_mobile_image' => $request->boolean('remove_mobile_image'),
            'delete_gallery' => $request->input('delete_gallery', []),
        ]);

        return redirect()->route('admin.events.index')
            ->with('status', __('event.messages.updated'));
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->persistEventMedia->delete($event);

        return redirect()->route('admin.events.index')
            ->with('status', __('event.messages.deleted'));
    }

    public function generateDeeplink(Event $event, EventRegistrationLinkService $links): RedirectResponse
    {
        $link = $links->issueOrRegenerate($event);

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', __('event.deeplink.generated'))
            ->with('deeplink_url', $link->shortUrl())
            ->with('deeplink_expires_at', $link->expires_at->timezone(config('app.timezone'))->format('d/m/Y H:i'));
    }
}
