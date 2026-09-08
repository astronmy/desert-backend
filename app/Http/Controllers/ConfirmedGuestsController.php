<?php

namespace App\Http\Controllers;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConfirmedGuestsController extends Controller
{
    public function __invoke(string $slug): View|RedirectResponse
    {
        if (! preg_match('/^(\d+)-(.+)$/', $slug, $matches)) {
            abort(404);
        }

        $event = Event::query()->findOrFail((int) $matches[1]);
        $canonical = $event->confirmedSiteSlug();

        if ($slug !== $canonical) {
            return redirect()->route('events.confirmed', $canonical, 301);
        }

        $invitations = Invitation::query()
            ->with('guest')
            ->where('event_id', $event->id)
            ->where('status', InvitationStatus::Confirmed)
            ->join('guests', 'guests.id', '=', 'invitations.guest_id')
            ->orderBy('guests.last_name')
            ->orderBy('guests.first_name')
            ->select('invitations.*')
            ->get();

        return view('public.confirmed-site', compact('event', 'invitations'));
    }
}
