<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvitationLogAction;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\InvitationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationLogController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        $logs = InvitationLog::query()
            ->with(['user', 'invitation.guest'])
            ->where('event_id', $event->id)
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $actions = InvitationLogAction::options();

        return view('admin.events.invitations.logs', compact('event', 'logs', 'actions'));
    }
}
