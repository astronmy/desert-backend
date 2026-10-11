<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class EventAccessController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $accesses = $event->accesses()
            ->with('invitation')
            ->when($request->filled('name'), function ($q) use ($request) {
                $name = '%'.$request->string('name').'%';
                $q->where(function ($inner) use ($name) {
                    $inner->where('guest_first_name', 'like', $name)
                        ->orWhere('guest_last_name', 'like', $name)
                        ->orWhereRaw("CONCAT(guest_first_name, ' ', guest_last_name) LIKE ?", [$name]);
                });
            })
            ->when($request->filled('document_number'), fn ($q) => $q->where('guest_document_number', 'like', '%'.$request->string('document_number').'%'))
            ->when($request->filled('code'), fn ($q) => $q->where('invitation_code', 'like', '%'.$request->string('code').'%'))
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $from = Carbon::parse($request->input('date_from'), Event::REGISTRATION_TIMEZONE)->startOfDay()->utc();
                $q->where('accessed_at', '>=', $from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $to = Carbon::parse($request->input('date_to'), Event::REGISTRATION_TIMEZONE)->endOfDay()->utc();
                $q->where('accessed_at', '<=', $to);
            })
            ->when($request->string('completion')->toString() === 'complete', function ($q) {
                $q->whereNotNull('entrada_at')->whereNotNull('salon_at');
            })
            ->when($request->string('completion')->toString() === 'partial', function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('entrada_at')->orWhereNull('salon_at');
                });
            })
            ->orderByDesc('accessed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.accesses.index', compact('event', 'accesses'));
    }
}
