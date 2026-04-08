@extends('layouts.main')

@section('title', 'Экспорты')

@section('content')
    <x-flash />
    <h2 class="text-xl font-bold mb-4">Экспорты</h2>
    <form class="text-lg font-bold mb-4 text-blue-600 hover:text-blue-800" action="{{ route('export.run') }}" method="POST">
        @csrf
        <button type="submit">
            Экспортировать каталог в S3
        </button>
    </form>
    <ul>
        <li class="flex flex-row font-bold p-2 w-full">
            <p class="w-1/4">Дата</p>
            <p class="w-1/2">Имя файла</p>
            <p class="w-1/4">Размер</p>
            <p class="w-1/4">Статус</p>
            <p>Действие</p>
        </li>
        @forelse($exports as $export)
            <li class="flex flex-row p-2 justify-between items-center even:bg-stone-200 ">
                <p class="w-1/4">{{ $export['date'] }}</p>
                <p class="w-1/2">{{ $export['name'] }}</p>
                <p class="w-1/4">{{ $export['size'] }}</p>
                <p class="w-1/4 mr-5"><span class="badge {{ $export['status']->color() }} py-1 px-2 rounded-lg">
                        {{ $export['status']->label() }}
                </p>
                <div class="flex flex-row">
                    <a title="Скачать" class="text-blue-600 hover:text-blue-800" href="{{ $export['url'] }}"
                        target="_blank">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    <form action="{{ route('export.destroy', $export['id']) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button title="Удалить" type="submit" class="ml-4 text-red-500 hover:text-red-700 cursor-pointer">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </li>
        @empty
            <li>
                <p colspan="4">Выгрузок пока не было</p>
            </li>
        @endforelse
    </ul>
    <div class="mt-4">
        {{ $exports->links() }}
    </div>
@endsection
