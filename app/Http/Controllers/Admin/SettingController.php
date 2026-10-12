<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(Request $request): View
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        return view('admin.settings.edit', [
            'setting' => Setting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdminRole(), 403);

        Setting::current()->update([
            'texto_registro' => $request->boolean('texto_registro'),
            'texto_confirmacion' => $request->boolean('texto_confirmacion'),
            'texto_recordatorio' => $request->boolean('texto_recordatorio'),
        ]);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', __('setting.messages.updated'));
    }
}
