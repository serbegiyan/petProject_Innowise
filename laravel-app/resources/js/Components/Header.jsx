import { usePage } from '@inertiajs/react';

export default function Header({ children }) {
    const { auth } = usePage().props;

    return (
        <header className="w-full h-14.5 flex flex-row justify-between py-1 px-4 bg-cyan-200 items-center">
            <img src="/images/logo.jpg" className="h-14 rounded-full" />
            {auth.user
                ? <p> Вы вошли как <span className='font-bold'>{auth.user.name}</span></p>
                : <p>Вы не авторизованы</p>
            }
            {children}
        </header >
    );
}