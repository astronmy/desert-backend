<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[var(--desert-bg)] border border-transparent rounded-md font-semibold text-xs text-[var(--desert-accent)] uppercase tracking-widest hover:bg-[var(--desert-bg-elevated)] focus:outline-none focus:ring-2 focus:ring-[var(--desert-accent)] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
