<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\StoreEventClientRequest;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class EventClientController extends Controller
{
    public function show(Event $event): JsonResponse
    {
        $user = $this->clientFor($event);

        if (! $user) {
            return response()->json([
                'exists' => false,
                'email' => null,
            ]);
        }

        return response()->json([
            'exists' => true,
            'email' => $user->email,
        ]);
    }

    public function store(StoreEventClientRequest $request, Event $event): JsonResponse
    {
        $existing = $this->clientFor($event);
        if ($existing) {
            return response()->json([
                'exists' => true,
                'email' => $existing->email,
                'password' => null,
                'message' => __('event.client.already_exists'),
            ], 409);
        }

        $role = Role::query()->where('slug', Role::SLUG_CLIENT)->first();
        if (! $role || ! $role->is_active) {
            return response()->json([
                'message' => __('event.client.role_missing'),
            ], 422);
        }

        $plain = $request->validated('password') ?: Str::password(12);

        $user = User::query()->create([
            'name' => $event->host ?: $event->name,
            'email' => $request->validated('email'),
            'password' => $plain,
            'role_id' => $role->id,
            'event_id' => $event->id,
        ]);

        return response()->json([
            'exists' => true,
            'email' => $user->email,
            'password' => $plain,
        ], 201);
    }

    public function regeneratePassword(Event $event): JsonResponse
    {
        $user = $this->clientFor($event);
        if (! $user) {
            return response()->json([
                'exists' => false,
                'message' => __('event.client.not_created'),
            ], 404);
        }

        $plain = Str::password(12);
        $user->update(['password' => $plain]);

        return response()->json([
            'exists' => true,
            'email' => $user->email,
            'password' => $plain,
        ]);
    }

    private function clientFor(Event $event): ?User
    {
        return $event->clients()
            ->whereHas('role', fn ($q) => $q->where('slug', Role::SLUG_CLIENT))
            ->latest('id')
            ->first();
    }
}
