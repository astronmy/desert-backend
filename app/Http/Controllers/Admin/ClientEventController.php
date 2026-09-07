<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\UpdateEventContentRequest;
use App\Models\Event;
use App\Services\Events\PersistEventMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientEventController extends Controller
{
    public function __construct(
        private readonly PersistEventMediaService $persistEventMedia
    ) {}

    public function edit(Request $request): View
    {
        $event = $this->assignedEvent($request);
        $event->load('images');

        return view('admin.client-event.edit', compact('event'));
    }

    public function update(UpdateEventContentRequest $request): RedirectResponse
    {
        $event = $this->assignedEvent($request);

        $this->persistEventMedia->update(
            $event,
            $request->safe()->only(['description', 'short_description']),
            [
                'image' => $request->file('image'),
                'mobile_image' => $request->file('mobile_image'),
                'gallery' => $request->file('gallery', []),
                'remove_image' => $request->boolean('remove_image'),
                'remove_mobile_image' => $request->boolean('remove_mobile_image'),
                'delete_gallery' => $request->input('delete_gallery', []),
            ]
        );

        return redirect()
            ->route('admin.client-event.edit')
            ->with('status', __('event.messages.updated'));
    }

    private function assignedEvent(Request $request): Event
    {
        $user = $request->user();
        abort_unless($user && $user->requiresEvent() && $user->event_id, 403, __('role.messages.client_needs_event'));

        $event = Event::query()->find($user->event_id);
        abort_unless($event, 404);

        return $event;
    }
}
