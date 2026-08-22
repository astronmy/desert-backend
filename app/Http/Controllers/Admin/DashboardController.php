<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $user?->loadMissing(['role.permissions', 'event']);

        $event = null;
        $invitations = null;
        $statuses = InvitationStatus::options();

        if ($user && $user->requiresEvent() && $user->event_id) {
            $event = Event::query()->find($user->event_id);

            if ($event && $user->canPermission('invitaciones.ver')) {
                $invitations = $event->invitations()
                    ->with('guest')
                    ->when($request->filled('name'), function ($q) use ($request) {
                        $name = '%'.$request->string('name').'%';
                        $q->whereHas('guest', fn ($g) => $g->where('first_name', 'like', $name)
                            ->orWhere('last_name', 'like', $name)
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$name]));
                    })
                    ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
                    ->latest()
                    ->paginate(15)
                    ->withQueryString();
            }
        }

        return view('dashboard', compact('event', 'invitations', 'statuses'));
    }
}
