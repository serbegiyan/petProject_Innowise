@props([
    'options' => [],    
    'valueField' => 'id', 
    'textField' => 'name',
    'selected' => null  
])

<select {{ $attributes->merge(['class' => 'border rounded-sm p-3 bg-white cursor-pointer']) }}>
    @if($slot->isNotEmpty())
        <option value="">{{ $slot }}</option>
    @endif

    @foreach ($options as $option)
        <option 
            value="{{ $option->$valueField }}" 
            {{ (old($attributes->get('name'), $selected) == $option->$valueField) ? 'selected' : '' }}
        >
            {{ $option->$textField }}
        </option>
    @endforeach
</select>