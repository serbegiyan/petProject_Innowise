<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Search extends Component
{
    public function __construct(
        public ?string $action = null,
        public ?string $value = null,
        public string $placeholder = 'Поиск',
        public string $ariaLabel = 'Поиск по товарам',
    ) {
        $this->action ??= route('product.index');
        $this->value ??= (string) request('search', '');
    }

    public function render(): View|Closure|string
    {
        return view('components.search');
    }
}
