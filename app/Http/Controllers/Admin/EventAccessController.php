<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventAccessController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $accesses = $event->accesses()
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
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('accessed_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('accessed_at', '<=', $request->date('date_to')))
            ->orderByDesc('accessed_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.accesses.index', compact('event', 'accesses'));
    }
}
