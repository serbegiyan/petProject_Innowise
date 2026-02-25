import Header from "@/Components/Header";
import { Head } from '@inertiajs/react';

export default function Index() {
    return (
        <>
            <Head title="Каталог" />
            <Header><NavBar /> </Header>
            <p>Hello from Basket</p>
        </>
    );
}