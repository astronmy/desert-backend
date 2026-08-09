<x-guest-layout>
    <x-auth-session-status class="mb-4 text-[var(--desert-bg)]" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[var(--desert-bg)]" />
            <x-text-input id="email" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-[var(--desert-bg)] focus:ring-[var(--desert-bg)]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[var(--desert-bg)]" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-[var(--desert-bg)]" />
            <x-text-input id="password" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-[var(--desert-bg)] focus:ring-[var(--desert-bg)]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[var(--desert-bg)]" />
        </div>

        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input id="remember" type="checkbox" class="rounded bg-white border-gray-300 text-[var(--desert-bg)] shadow-sm focus:ring-[var(--desert-bg)]" name="remember">
                <span class="ms-2 text-sm text-[var(--desert-bg)]">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="!bg-[var(--desert-bg)] !text-[var(--desert-accent)] hover:!bg-[var(--desert-bg-elevated)]">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
