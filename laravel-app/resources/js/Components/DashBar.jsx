
export default function DashBar() {
    return (
        <nav>
            <a
                className=""
                href={route('product.index')}
                class="hover:underline inline-flex items-center text-lg px-1 pt-1 text">                Продукты
            </a>
            <a
                href={route('service.index')}
                class="hover:underline inline-flex items-center text-lg px-1 pt-1 text">                Услуги
            </a>
            <a
                href={route('category.index')}
                class="hover:underline inline-flex items-center text-lg px-1 pt-1 text">                Категории
            </a>
            <a href="{{ route('logout') }}"
                class="hover:underline inline-flex items-center text-lg px-1 pt-1 text">
                Выйти
            </a>
        </nav >
    );
}