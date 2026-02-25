@extends('layouts.main')

@section('title', 'Просмотр услуги: ' . $service->name)

@section('content')
    <h2 class="text-xl font-bold mb-4">Просмотр услуги: {{ $service->name }}</h2>
    <x-title-link href="{{ route('service.index') }}">Вернуться к списку услуг</x-title-link>
    
    <p class="p-2"><span class="font-bold">Название услуги:</span> {{ $service->name }}</p>
    <p class="p-2"><span class="font-bold">Slug:</span> {{ $service->slug }}</p>
    <p class="p-2"><span class="font-bold">Описание услуги:</span> {{ $service->description }}</p>

@endsection
