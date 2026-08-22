<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with(['role', 'event'])
            ->when($request->filled('name'), fn ($q) => $q->where('name', 'like', '%'.$request->string('name').'%'))
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%'.$request->string('email').'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('admin.users.index')
            ->with('status', __('user.messages.created'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', array_merge($this->formData(), compact('user')));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('status', __('user.messages.updated'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', __('user.messages.cannot_delete_self'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', __('user.messages.deleted'));
    }

    /**
     * @return array{roles: \Illuminate\Database\Eloquent\Collection<int, Role>, events: \Illuminate\Database\Eloquent\Collection<int, Event>, rolesMeta: array<int, array{requires_event: bool}>}
     */
    private function formData(): array
    {
        $roles = Role::query()->where('is_active', true)->orderBy('name')->get();
        $events = Event::query()->orderByDesc('init_date')->get(['id', 'name']);

        $rolesMeta = $roles->mapWithKeys(fn (Role $role) => [
            $role->id => ['requires_event' => $role->requires_event],
        ])->all();

        return compact('roles', 'events', 'rolesMeta');
    }
}
