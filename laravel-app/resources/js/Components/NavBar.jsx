import NavLink from "./NavLink"

export default function NavBar() {
    return (
        <nav>
            <NavLink
                href={route('catalog.index')}
                active={route().current('catalog.index')}>
                Каталог
            </NavLink>
            <NavLink
                href={route('basket.index')}
                active={route().current('basket.index')}>
                Корзина
            </NavLink>
        </nav>
    );
}