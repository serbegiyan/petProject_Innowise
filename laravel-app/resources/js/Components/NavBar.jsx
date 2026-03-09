import NavLink from "./NavLink"
import { usePage } from '@inertiajs/react';
import { Link } from "@inertiajs/react";

export default function NavBar() {
    const { auth } = usePage().props;
    const activeClass = 'border-indigo-400 px-1 text-lg text-gray-900 focus:border-indigo-700';
    const passiveClass = 'border-transparent px-1 text-lg text-gray-500 hover:border-b-2 hover:border-gray-300 hover:text-gray-700';

    return (
        <nav>
            <NavLink
                href={route('catalog.index')}
                active={route().current('catalog.index')}>
                <i className="mr-1 fa-solid fa-store"></i>Каталог
            </NavLink>
            <NavLink
                href={route('basket.index')}
                active={route().current('basket.index')}>

                <i className="mr-1 fa-solid fa-basket-shopping"></i>Корзина
                {auth.user?.basket_count > 0 && (
                    <span className="ml-1 bg-rose-400 text-white text-sm font-medium px-2 py-0.5 rounded-full">
                        {auth.user.basket_count}
                    </span>
                )}
            </NavLink>
            {auth.user && auth.user.role === 'admin' &&
                <>
                    <a
                        href={route('admin.main')}
                        className={route().current('admin.main') ? activeClass : passiveClass}>
                        <i className="mr-1 fa-solid fa-user-gear"></i>Админка
                    </a>
                </>
            }
            {auth.user ? (
                <>
                    <NavLink href={route('dashboard')}
                        active={route().current('dashboard')}
                    >
                        <i className="mr-1 fa-solid fa-circle-user"></i>Профиль</NavLink>

                    <Link
                        className="text-gray-500 leading-5 hover:border-b-2 text-lg hover:text-gray-700 hover:border-gray-300"
                        href={route('logout')} method="post" as="button">
                        <i className="mr-1 fa-solid fa-right-from-bracket"></i>Выйти</Link>
                </>
            ) : (
                <>
                    <NavLink href={route('login')} active={route().current('login')}>
                        <i className="mr-1 fa-solid fa-right-to-bracket"></i>Войти
                    </NavLink>
                    <NavLink href={route('register')} active={route().current('register')}>
                        <i className="mr-1 fa-solid fa-user-plus"></i>Регистрация
                    </NavLink>
                </>
            )}

        </nav>
    );
}