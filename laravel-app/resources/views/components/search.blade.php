<form class="flex flex-col justify-around" action="{{ $action }}" method="GET">
    <div class="flex flex-row">
        <input type="text" name="search" value="{{ $value }}" placeholder="{{ $placeholder }}"
            aria-label="{{ $ariaLabel }}"
            class="bg-stone-300 border rounded-lg p-3 w-full">
        <button type="submit" class="-ml-14 w-fit px-4 rounded" aria-label="Искать">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
</form>
