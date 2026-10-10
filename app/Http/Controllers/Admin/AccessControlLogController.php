<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessControlLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessControlLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        $logs = AccessControlLog::query()
            ->with(['user', 'event'])
            ->when($request->filled('service'), fn ($q) => $q->where('service', $request->string('service')))
            ->when($request->filled('method'), fn ($q) => $q->where('method', strtoupper((string) $request->string('method'))))
            ->when($request->filled('response_code'), fn ($q) => $q->where('response_code', $request->integer('response_code')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.access-control-logs.index', compact('logs'));
    }
}
