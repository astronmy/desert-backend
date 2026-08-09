@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge([
    'class' => 'block w-full appearance-none rounded-md border border-gray-300 bg-white bg-[length:1rem] bg-[right_0.75rem_center] bg-no-repeat pr-10 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm text-gray-900',
    'style' => "background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23273C3B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E\");",
]) }}>
    {{ $slot }}
</select>
