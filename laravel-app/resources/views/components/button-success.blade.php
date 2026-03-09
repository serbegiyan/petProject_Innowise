<button {{ $attributes->merge(['class' => 'bg-blue-600 text-white hover:bg-blue-700 w-fit px-4 py-2 rounded']) }}
    type="submit">
    {{ $slot }}
</button>
