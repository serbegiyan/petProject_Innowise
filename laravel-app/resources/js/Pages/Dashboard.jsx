import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import NavBar from '@/Components/NavBar';

export default function Dashboard() {
    return (
        <>
            <Header><NavBar /></Header>
            <Head title="Dashboard" />

            <div className="py-12">
                Вы успешно вошли в систему!
            </div>
        </>
    );
}
