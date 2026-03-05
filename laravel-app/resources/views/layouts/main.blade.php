<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/alert.js'])
    <title>@yield('title')</title>

    <!-- Fonts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Styles / Scripts -->

</head>

<body class="bg-stone-100 min-h-screen">
    <header class="w-full bg-stone-300 h-14.5 flex flex-row justify-between">
        <img src="/images/logo.jpg" className="w-10 rounded-full" />
        <div class=" p-4 ">Вы вошли как {{ auth()->user()->name }}</div>
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4 mr-4">
                @auth
                    <a href="{{ route('admin.main') }}" class="hover:underline inline-flex items-center text-lg pt-1 text">
                        <i class="mr-1 fa-solid fa-house"></i>
                        Главная
                    </a>
                    <a href="{{ route('catalog.index') }}"
                        class="hover:underline inline-flex items-center text-lg pt-1 text">
                        <i class="mr-1 fa-solid fa-store"></i>
                        Магазин
                    </a>
                    <a href="{{ url('/dashboard') }}" class="hover:underline inline-flex items-center text-lg pt-1 text">
                        <i class="mr-1 fa-solid fa-circle-user"></i>
                        Профиль
                    </a>
                    <a href="{{ route('logout') }}" class="hover:underline inline-flex items-center text-lg pt-1 text">
                        <i class="mr-1 fa-solid fa-right-from-bracket"></i>
                        Выйти
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Войти
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Регистрация
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>
    <div class="container flex flex-row min-h-screen">
        <div class="w-1/6 p-4 bg-stone-200">
            <ul class="flex flex-col gap-2 ">
                <li>
                    <a href="{{ route('product.index') }}" @class([
                        'block px-4 py-2 rounded-sm hover:bg-stone-300',
                        'block px-4 py-2 underline-offset-4 underline' => request()->routeIs(
                            'product.*'),
                    ])><i
                            class="mr-2 fa-solid fa-bag-shopping"></i>
                        Продукты ({{ $sidebar_stats['products_count'] }})
                    </a>
                    @if (request()->routeIs('product.*'))
                        @foreach ($navCategories as $cat)
                <li class="">
                    <a href="{{ route('product.index', ['category_id' => $cat->id]) }}" @class([
                        'block pl-12 rounded-sm hover:bg-stone-300',
                        'block pl-12 underline-offset-4 underline' =>
                            request('category_id') == $cat->id,
                    ])>
                        <p>&bull; {{ $cat->name }} ({{ $cat->products_count }})</p>
                    </a>
                </li>
                @endforeach
                @endif
                </li>
                <li>
                    <a href="{{ route('service.index') }}" @class([
                        'block px-4 py-2 rounded-sm hover:bg-stone-300',
                        'block px-4 py-2 underline-offset-4 underline' => request()->routeIs(
                            'service.*'),
                    ])><i
                            class="mr-2 fa-solid fa-screwdriver-wrench"></i>
                        Услуги ({{ $sidebar_stats['services_count'] }})
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.index') }}" @class([
                        'block px-4 py-2 rounded-sm hover:bg-stone-300',
                        'block px-4 py-2 underline-offset-4 underline' => request()->routeIs(
                            'category.*'),
                    ])><i
                            class="mr-2 fa-solid fa-layer-group"></i>
                        Категории ({{ $sidebar_stats['categories_count'] }})
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.index') }}" @class([
                        'block px-4 py-2 rounded-sm hover:bg-stone-300',
                        'block px-4 py-2 underline-offset-4 underline' => request()->routeIs(
                            'category.*'),
                    ])><i
                            class="mr-2 fa-solid fa-users"></i></i>
                        Пользователи ({{ $sidebar_stats['users_count'] }})
                    </a>
                </li>
                <li>
                    <a href="{{ route('category.index') }}" @class([
                        'block px-4 py-2 rounded-sm hover:bg-stone-300',
                        'block px-4 py-2 underline-offset-4 underline' => request()->routeIs(
                            'category.*'),
                    ])><i
                            class="mr-2 fa-solid fa-file-invoice-dollar"></i></i>
                        Заказы ({{ $sidebar_stats['users_count'] }})
                    </a>
                </li>
            </ul>
        </div>
        <div class="w-5/6 p-4 bg-stone-100">
            @yield('content')
        </div>
    </div>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
    @stack('scripts')
</body>

</html>
