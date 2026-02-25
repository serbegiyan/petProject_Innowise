import { Head } from '@inertiajs/react';
import Header from '@/Components/Header';
import DashBar from '@/Components/DashBar';

export default function Dashboard() {
    return (
        <>
            <Header><DashBar /></Header>
            <Head title="Dashboard" />

            <div className="py-12">
                Вы успешно вошли в систему!
            </div>
        </>
    );
}
