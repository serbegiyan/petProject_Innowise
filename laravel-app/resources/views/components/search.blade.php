<form class="flex flex-col justify-around " action="{{ route('product.index') }}" method="GET">
    <div class="flex flex-row">
        <input type="text" name="search" placeholder="Поиск" class="bg-stone-300 border rounded-lg p-2 w-full">
        <button type="submit" class="-ml-14 w-fit px-4 rounded">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
</form>
