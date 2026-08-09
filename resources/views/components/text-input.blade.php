@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-gray-300 focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] rounded-md shadow-sm']) }}>
