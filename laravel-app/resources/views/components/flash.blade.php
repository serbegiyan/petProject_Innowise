@if (session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 flash-message"
        role="alert">
        <span class="font-bold">Успешно!</span> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flash-message" role="alert">
        <span class="font-bold">Ошибка!</span> {{ session('error') }}
    </div>
@endif
@push('scripts')
    @vite(['resources/js/alert.js'])
@endpush
