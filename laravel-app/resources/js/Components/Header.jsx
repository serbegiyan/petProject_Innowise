import { usePage } from '@inertiajs/react';
import NavBar from '@/Components/NavBar';

export default function Header({ searchSlot, currencySlot }) {
    const { auth } = usePage().props;

    return (
        <header className="bg-cyan-200 h-14.5 flex flex-row justify-between py-1 px-4 items-center gap-4 border-b border-cyan-300">
            <img src="/images/logo.jpg" className="h-12 rounded-full" alt="logo" />
            <div className="whitespace-nowrap hidden md:block">
                {auth.user
                    ? <p>Вы вошли как <span className='font-bold'>{auth.user.name}</span></p>
                    : <p>Вы не авторизованы</p>
                }
            </div>
            <div className="mx-4">
                {searchSlot}
            </div>

            <div className="items-center gap-4">
                {currencySlot}
            </div>
            <NavBar />
        </header>
    );
}
