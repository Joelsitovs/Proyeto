@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2  border-black text-sm font-medium leading-5 text-black focus:outline-none focus:border-gray-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 hover:border-black  ease-in-out transition duration-150 text-sm font-medium leading-5 text-black focus:outline-none ';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
