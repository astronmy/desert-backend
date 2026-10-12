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

        $messages = $request->validate([
            'mensaje_registro' => ['nullable', 'string', 'max:1000'],
            'mensaje_confirmacion' => ['nullable', 'string', 'max:1000'],
            'mensaje_recordatorio' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::current()->update([
            'texto_registro' => $request->boolean('texto_registro'),
            'texto_confirmacion' => $request->boolean('texto_confirmacion'),
            'texto_recordatorio' => $request->boolean('texto_recordatorio'),
            'mensaje_registro' => $this->message($messages['mensaje_registro'] ?? null),
            'mensaje_confirmacion' => $this->message($messages['mensaje_confirmacion'] ?? null),
            'mensaje_recordatorio' => $this->message($messages['mensaje_recordatorio'] ?? null),
        ]);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', __('setting.messages.updated'));
    }

    private function message(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
