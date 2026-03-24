@props([
    'options' => [],
    'valueField' => 'id',
    'textField' => 'name',
    'selected' => null,
])

<select {{ $attributes->merge(['class' => 'border rounded-lg p-3 cursor-pointer']) }}>
    @if ($slot->isNotEmpty())
        <option value="">{{ $slot }}</option>
    @endif

    @foreach ($options as $option)
        @php
            // Выносим значение в переменную для читаемости и избежания дублирования ошибок
            $currentValue = $option?->{$valueField};
        @endphp
        <option value="{{ $currentValue }}"
            {{ old($attributes->get('name'), $selected) == $option->$valueField ? 'selected' : '' }}>
            {{ $option->$textField }}
        </option>
    @endforeach
</select>
