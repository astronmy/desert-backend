<?php

namespace App\Http\Controllers\Admin;

use App\Exports\GraduateListMappingExport;
use App\Http\Controllers\Controller;
use App\Services\Tools\MapGraduateList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ToolController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        return view('admin.tools.index');
    }

    public function listMapping(Request $request): View
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        return view('admin.tools.list-mapping');
    }

    public function mapList(Request $request, MapGraduateList $mapper): BinaryFileResponse|RedirectResponse
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $rows = $mapper->rows($data['file']);

        if ($rows === []) {
            return redirect()
                ->route('admin.tools.list-mapping')
                ->with('error', __('tool.list_mapping.empty'));
        }

        return Excel::download(new GraduateListMappingExport($rows), 'mapeo-listas.xlsx');
    }
}
